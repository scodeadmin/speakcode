<?php
/*
    openQRM Enterprise developed by OPENQRM AUSTRALIA PTY LTD.

    All source code and content (c) Copyright 2021, OPENQRM AUSTRALIA PTY LTD unless specifically noted otherwise.

    This source code is released under the GNU General Public License version 2, unless otherwise agreed with OPENQRM AUSTRALIA PTY LTD.
    The latest version of this license can be found here: src/doc/LICENSE.txt

    By using this software, you acknowledge having read this license and agree to be bound thereby.

                http://openqrm-enterprise.com

    Copyright 2021, OPENQRM AUSTRALIA PTY LTD <info@openqrm-enterprise.com>
*/


// This file implements the cloud storage methods

$RootDir = $_SERVER["DOCUMENT_ROOT"].'/openqrm/base/';
require_once "$RootDir/include/user.inc.php";
require_once "$RootDir/include/openqrm-database-functions.php";
require_once "$RootDir/class/resource.class.php";
require_once "$RootDir/class/image.class.php";
require_once "$RootDir/class/storage.class.php";
require_once "$RootDir/class/deployment.class.php";
require_once "$RootDir/class/appliance.class.php";
require_once "$RootDir/class/openqrm_server.class.php";
require_once "$RootDir/class/plugin.class.php";
require_once "$RootDir/class/event.class.php";
// special cloud classes
require_once "$RootDir/plugins/cloud/class/cloudimage.class.php";

$event = new event();
global $event;

global $OPENQRM_SERVER_BASE_DIR;
$openqrm_server = new openqrm_server();
$OPENQRM_SERVER_IP_ADDRESS=$openqrm_server->get_ip_address();
global $OPENQRM_SERVER_IP_ADDRESS;
global $RESOURCE_INFO_TABLE;


// ---------------------------------------------------------------------------------
// general cloudstorage methods
// ---------------------------------------------------------------------------------


// clones the volume of an image
function create_clone_kvm_gluster_deployment($cloud_image_id, $image_clone_name, $disk_size) {
	global $OPENQRM_SERVER_BASE_DIR;
	global $OPENQRM_SERVER_IP_ADDRESS;
	global $OPENQRM_EXEC_PORT;
	global $RESOURCE_INFO_TABLE;
	global $event;

	// we got the cloudimage id here, get the image out of it
	$cloudimage = new cloudimage();
	$cloudimage->get_instance_by_id($cloud_image_id);
	$event->log("create_clone_kvm_gluster_deployment", $_SERVER['REQUEST_TIME'], 5, "openqrm-kvm-gluster-deployment-cloud-hook.php", "Creating clone ".$image_clone_name." of image ".$cloudimage->image_id." on the storage", "", "", 0, 0, 0);
	// get image, this is already the new logical clone
	// we just need to physical snapshot it and update the rootdevice
	$image = new image();
	$image->get_instance_by_id($cloudimage->image_id);
	$image_id = $image->id;
	$image_name = $image->name;
	$image_type = $image->type;
	$image_version = $image->version;
	$image_rootdevice = $image->rootdevice;
	$image_rootfstype = $image->rootfstype;
	$imageid = $image->storageid;
	$image_isshared = $image->isshared;
	$image_comment = $image->comment;
	$image_capabilities = $image->capabilities;
	$image_deployment_parameter = $image->deployment_parameter;

	// get image storage
	$storage = new storage();
	$storage->get_instance_by_id($imageid);
	$storage_resource_id = $storage->resource_id;
	// get deployment type
	$deployment = new deployment();
	$deployment->get_instance_by_id($storage->type);
	// get storage resource
	$resource = new resource();
	$resource->get_instance_by_id($storage_resource_id);
	$resource_id = $resource->id;
	$resource_ip = $resource->ip;
	// kvm-gluster-deployment
	$image->get_instance_by_id($image_id);
	$gluster_uri_arr = parse_url($image_rootdevice);
	// origin image volume name
	$origin_volume_name=basename($gluster_uri_arr['path']);
	// location of the volume (path)
	$image_location_name=str_replace('/', '', dirname($gluster_uri_arr['path']));
	// set default snapshot size
	if (!strlen($disk_size)) {
		$disk_size=5000;
	}
	// update the image rootdevice parameter
	$ar_image_update = array(
		'image_rootdevice' => "gluster:".$resource->ip."//".$image_location_name."/".$image_clone_name,
	);
	// get the vm resource
	$vm_resource = new resource();
	$vm_resource->get_instance_by_id($cloudimage->resource_id);
	// get the VM host
	$vm_host_resource = new resource();
	$vm_host_resource->get_instance_by_id($vm_resource->vhostid);

	$event->log("create_clone_kvm_gluster_deployment", $_SERVER['REQUEST_TIME'], 5, "openqrm-kvm-gluster-deployment-cloud-hook.php", "Updating rootdevice of image ".$image_id." / ".$image_name." with ".$image_location_name."/".$image_clone_name, "", "", 0, 0, 0);
	$image->update($image_id, $ar_image_update);
	$image_clone_cmd="$OPENQRM_SERVER_BASE_DIR/openqrm/plugins/kvm/bin/openqrm-kvm snap -n ".$origin_volume_name." -v ".$image_location_name." -s ".$image_clone_name." -m ".$disk_size." -t ".$deployment->type." --openqrm-cmd-mode background";
	$event->log("create_clone_kvm_gluster_deployment", $_SERVER['REQUEST_TIME'], 5, "openqrm-kvm-gluster-deployment-cloud-hook.php", "Running : ".$image_clone_cmd, "", "", 0, 0, 0);
	$resource->send_command($resource->ip, $image_clone_cmd);
}



// removes the volume of an image
function remove_kvm_gluster_deployment($cloud_image_id) {
	global $OPENQRM_SERVER_BASE_DIR;
	global $OPENQRM_SERVER_IP_ADDRESS;
	global $OPENQRM_EXEC_PORT;
	global $RESOURCE_INFO_TABLE;
	global $event;

	$cloudimage = new cloudimage();
	$cloudimage->get_instance_by_id($cloud_image_id);
	$event->log("remove_kvm_gluster_deployment", $_SERVER['REQUEST_TIME'], 5, "openqrm-kvm-gluster-deployment-cloud-hook.php", "Removing image ".$cloudimage->image_id." from storage.", "", "", 0, 0, 0);
	// get image
	$image = new image();
	$image->get_instance_by_id($cloudimage->image_id);
	$image_id = $image->id;
	$image_name = $image->name;
	$image_type = $image->type;
	$image_version = $image->version;
	$image_rootdevice = $image->rootdevice;
	$image_rootfstype = $image->rootfstype;
	$imageid = $image->storageid;
	$image_isshared = $image->isshared;
	$image_comment = $image->comment;
	$image_capabilities = $image->capabilities;
	$image_deployment_parameter = $image->deployment_parameter;

	// get image storage
	$storage = new storage();
	$storage->get_instance_by_id($imageid);
	$storage_resource_id = $storage->resource_id;
	// get deployment type
	$deployment = new deployment();
	$deployment->get_instance_by_id($storage->type);
	// get storage resource
	$resource = new resource();
	$resource->get_instance_by_id($storage_resource_id);
	$resource_id = $resource->id;
	$resource_ip = $resource->ip;
	$gluster_uri_arr = parse_url($image_rootdevice);
	// origin image volume name
	$origin_volume_name=basename($gluster_uri_arr['path']);
	// location of the volume (path)
	$image_location_name=str_replace('/', '', dirname($gluster_uri_arr['path']));
	$image_remove_clone_cmd=$OPENQRM_SERVER_BASE_DIR."/openqrm/plugins/kvm/bin/openqrm-kvm remove -n ".$origin_volume_name." -v ".$image_location_name." -t ".$deployment->type." --openqrm-cmd-mode background";
	$event->log("remove_kvm_gluster_deployment", $_SERVER['REQUEST_TIME'], 5, "openqrm-kvm-gluster-deployment-cloud-hook.php", "Running : ".$image_remove_clone_cmd, "", "", 0, 0, 0);
	$resource->send_command($resource->ip, $image_remove_clone_cmd);
}


// resizes the volume of an image
function resize_kvm_gluster_deployment($cloud_image_id, $resize_value) {
	global $OPENQRM_SERVER_BASE_DIR;
	global $OPENQRM_SERVER_IP_ADDRESS;
	global $OPENQRM_EXEC_PORT;
	global $RESOURCE_INFO_TABLE;
	global $event;

	$cloudimage = new cloudimage();
	$cloudimage->get_instance_by_id($cloud_image_id);
	$event->log("resize_kvm_gluster_deployment", $_SERVER['REQUEST_TIME'], 5, "openqrm-kvm-gluster-deployment-cloud-hook.php", "Resize image ".$cloudimage->image_id." on storage.", "", "", 0, 0, 0);
	// get image
	$image = new image();
	$image->get_instance_by_id($cloudimage->image_id);
	$image_id = $image->id;
	$image_name = $image->name;
	$image_type = $image->type;
	$image_version = $image->version;
	$image_rootdevice = $image->rootdevice;
	$image_rootfstype = $image->rootfstype;
	$imageid = $image->storageid;
	$image_isshared = $image->isshared;
	$image_comment = $image->comment;
	$image_capabilities = $image->capabilities;
	$image_deployment_parameter = $image->deployment_parameter;

	// get image storage
	$storage = new storage();
	$storage->get_instance_by_id($imageid);
	$storage_resource_id = $storage->resource_id;
	// get deployment type
	$deployment = new deployment();
	$deployment->get_instance_by_id($storage->type);
	// get storage resource
	$resource = new resource();
	$resource->get_instance_by_id($storage_resource_id);
	$resource_id = $resource->id;
	$resource_ip = $resource->ip;
	$gluster_uri_arr = parse_url($image_rootdevice);
	// origin image volume name
	$origin_volume_name=basename($gluster_uri_arr['path']);
	// location of the volume (path)
	$image_location_name=str_replace('/', '', dirname($gluster_uri_arr['path']));
	$image_resize_cmd=$OPENQRM_SERVER_BASE_DIR."/openqrm/plugins/kvm/bin/openqrm-kvm resize -n ".$origin_volume_name." -v ".$image_location_name." -m ".$resize_value." -t ".$deployment->type." --openqrm-cmd-mode background";
	$event->log("resize_kvm_gluster_deployment", $_SERVER['REQUEST_TIME'], 5, "openqrm-kvm-gluster-deployment-cloud-hook.php", "Running : ".$image_resize_cmd, "", "", 0, 0, 0);
	$resource->send_command($resource->ip, $image_resize_cmd);
}



// creates a private copy of the volume of an image
function create_private_kvm_gluster_deployment($cloud_image_id, $private_disk, $private_image_name) {
	global $OPENQRM_SERVER_BASE_DIR;
	global $OPENQRM_SERVER_IP_ADDRESS;
	global $OPENQRM_EXEC_PORT;
	global $RESOURCE_INFO_TABLE;
	global $event;

	$cloudimage = new cloudimage();
	$cloudimage->get_instance_by_id($cloud_image_id);
	$event->log("create_private_kvm_gluster_deployment", $_SERVER['REQUEST_TIME'], 5, "openqrm-kvm-gluster-deployment-cloud-hook.php", "Creating private image ".$cloudimage->image_id." on storage.", "", "", 0, 0, 0);
	// get image
	$image = new image();
	$image->get_instance_by_id($cloudimage->image_id);
	$image_id = $image->id;
	$image_name = $image->name;
	$image_type = $image->type;
	$image_version = $image->version;
	$image_rootdevice = $image->rootdevice;
	$image_rootfstype = $image->rootfstype;
	$imageid = $image->storageid;
	$image_isshared = $image->isshared;
	$image_comment = $image->comment;
	$image_capabilities = $image->capabilities;
	$image_deployment_parameter = $image->deployment_parameter;

	// get image storage
	$storage = new storage();
	$storage->get_instance_by_id($imageid);
	$storage_resource_id = $storage->resource_id;
	// get deployment type
	$deployment = new deployment();
	$deployment->get_instance_by_id($storage->type);
	// get storage resource
	$resource = new resource();
	$resource->get_instance_by_id($storage_resource_id);
	$resource_id = $resource->id;
	$resource_ip = $resource->ip;
	// create an admin user to post when cloning has finished
	$openqrm_admin_user = new user("openqrm");
	$openqrm_admin_user->set_user();
	$gluster_uri_arr = parse_url($image_rootdevice);
	// origin image volume name
	$origin_volume_name=basename($gluster_uri_arr['path']);
	// location of the volume (path)
	$image_location_name=str_replace('/', '', dirname($gluster_uri_arr['path']));
	$image_clone_cmd=$OPENQRM_SERVER_BASE_DIR."/openqrm/plugins/kvm/bin/openqrm-kvm clone -n ".$origin_volume_name." -s ".$private_image_name." -v ".$image_location_name." -m ".$private_disk." -u ".$openqrm_admin_user->name." -p ".$openqrm_admin_user->password." -t ".$deployment->type." --openqrm-cmd-mode background";
	$event->log("create_private_kvm_gluster_deployment", $_SERVER['REQUEST_TIME'], 5, "openqrm-kvm-gluster-deployment-cloud-hook.php", "Running : $image_resize_cmd", "", "", 0, 0, 0);
	$resource->send_command($resource->ip, $image_clone_cmd);
	// set the storage specific image root_device parameter
	$new_rootdevice = "gluster:".$resource->ip."//".$image_location_name."/".$private_image_name;
	return $new_rootdevice;
}



// ---------------------------------------------------------------------------------


