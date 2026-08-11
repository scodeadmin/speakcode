# An Idea, Not a Project

## What this is

This repo used to carry a full source copy of openQRM 5.3.50 Community Edition, committed directly, 14MB of it. I removed it. Nothing in SpeakCode referenced it, and vendoring GPL source inside an Apache-2.0 repo is a question nobody needed to answer.

But removing it left something worth writing down. openQRM did a job that has not been fully replaced, and I want to put the idea on paper.

To be clear about what this document is: an idea I am thinking through in public. I am not building it. If someone else wants to, take it.

## What openQRM did

It managed heterogeneous data center infrastructure from one place. Bare metal and virtual machines, across Xen, KVM, VMware, and others. Diskless boot, so servers provisioned without local storage. Storage plugins, IP management, monitoring folded in, configuration management through Puppet.

One control plane over hardware you already had, whatever shape it was in.

Development slowed. The last community release was 5.3.60 in August 2024, and it was mostly dependency updates and PHP 8 compatibility. The project changed hands in 2020. It is still maintained, but it is not moving.

## What replaced it, and what did not

Kubernetes took the workload orchestration piece and took it decisively. If your problem is scheduling containers across a fleet, that argument is settled.

What did not get replaced cleanly:

* **Bare metal provisioning** as a first-class concern rather than a prerequisite you solve separately.
* **Mixed estates.** Physical servers, VMs on two different hypervisors, and containers, managed together instead of through three tools.
* **Small operators.** Kubernetes assumes scale and a platform team. A lot of infrastructure is neither.

Tools exist for each piece. MAAS and Tinkerbell for metal. Proxmox for VMs. Terraform for declaration. Ansible for configuration. They are good tools. They are also separate tools, and the person holding them together is doing integration work that openQRM did in one place.

That gap is the idea.

## The shape of it

If someone built this now, I do not think it looks like openQRM rewritten. It looks like this:

**Declarative, not clickable.** Infrastructure state in version control, reconciled continuously. openQRM was a web console over imperative actions. Everything since has moved the other way, and for good reason: the console does not tell you why something is the way it is.

**Provisioning as a plugin surface, not a core.** Metal, hypervisors, and cloud providers behind one interface. The value is the abstraction, not any single backend.

**Inventory as the primary object.** Most infrastructure pain is not deployment, it is not knowing what you have. What is running, on what, since when, patched to what level, owned by whom.

**Boring by default.** Infrastructure tooling that requires its own platform team has recreated the problem it solved.

**Honest about scope.** Not a cloud. Not a Kubernetes competitor. A management layer for infrastructure that is not going to be containerized, which is more of it than the conference talks suggest.

## Why this belongs in a repo about writing code

Because the reuse question runs through it.

Every piece of the above exists somewhere. The work is not invention, it is composition and judgment. Which projects are maintained. Which licenses compose. What you take as a dependency versus what you own.

A model will generate a provisioning script in seconds. It will not tell you whether MAAS is still maintained, whether the license fits, or whether your team can operate it at 3am. That is context, and context is the whole job.

## Open questions

Things I do not have answers to:

* Is the mixed-estate problem large enough to sustain a project, or is everyone quietly living with three tools?
* Does inventory-first inevitably become a CMDB, and is that a good thing or the failure mode?
* What license makes this composable rather than cornered?
* Is the honest answer just better documentation for tools that already exist?

That last one is real. Sometimes the gap is not a missing tool.

## If you want to take this

It is yours. Open an issue if you want to argue with any of it, or tell me it already exists and I missed it. That is a useful outcome too.

## References

* openQRM Community Edition: https://openqrm-enterprise.com/community-edition/
* MAAS: https://maas.io/
* Tinkerbell: https://tinkerbell.org/
* Proxmox VE: https://www.proxmox.com/en/proxmox-virtual-environment/overview
