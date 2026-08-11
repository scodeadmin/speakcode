# Getting Started with GitHub

Setup notes for anyone contributing to this repo for the first time. If you already work in GitHub daily, skip this and read [CONTRIBUTING.md](./CONTRIBUTING.md) instead.

## Create an account and set up your profile

Need a GitHub account? [Create one](https://github.com/join). Pick a username you will be comfortable with in a few years, since it follows your commits around.

Fill out the profile. It is how you communicate what you are interested in and what you like to work on. You can monitor your activity through the [website](https://github.com/) or the [mobile app](https://github.com/mobile).

> [!NOTE]
> Profiles are public. Do not put personal information in your profile or your commits. The entire GitHub community can see it, and commit history is difficult to clean up after the fact.

One setting worth checking before your first commit: **Settings > Emails > Keep my email addresses private**. Without it, whatever email is in your local git config gets published with every commit you make.

## Setting up your tools

### Git

[Git](https://git-scm.com/downloads) is the command-line tool for submitting pull requests, managing branches, and updating your local copy of the repo. Documentation for [how to use Git](https://git-scm.com/doc) is online, and it is good.

freeCodeCamp keeps a [list of Git commands](https://www.freecodecamp.org/news/10-important-git-commands-that-every-developer-should-know/) worth knowing early.

### Visual Studio Code

[VS Code](https://code.visualstudio.com/) runs on Windows, Mac, and Linux. It is a lean editor with an extension marketplace, and it handles GitHub work without leaving the application, including publishing branches.

There is a [walkthrough for your first pull request](https://code.visualstudio.com/docs/editor/github) in their docs.

Get a local copy of the repo before you start working on anything.

> [!NOTE]
> Create branches and pull requests in your own fork. You will not commit directly to `main`. Pull `main` into your local copy regularly so you are working against current content. It reduces the risk of conflicts.

### GitHub Desktop

If you are new to GitHub or want a visual read on what is happening in a repo, [GitHub Desktop](https://desktop.github.com/) shows history, your local changes, and your branches at a glance. It connects directly to the website and publishes branches in one click.

See [contributing and collaborating using GitHub Desktop](https://docs.github.com/en/desktop/overview/getting-started-with-github-desktop).

### Working in the browser

You do not have to install anything to make a small edit. GitHub's web editor handles single-file changes, and pressing `.` in any repo opens a full editor in the browser. For a one-file learning path submission, that is often enough.

## Keep your local copy current

Update your local copy before you create or update a pull request. It reduces the chance of a [merge conflict](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/addressing-merge-conflicts/about-merge-conflicts).

```bash
$ git checkout main
$ git pull upstream main
```

If `upstream` is not set yet, that is the original repo you forked from. GitHub documents [configuring a remote for a fork](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/working-with-forks/configuring-a-remote-repository-for-a-fork).

## Reference

This list changes as tools and docs move. If a link is dead, open an issue or send a replacement.

- [GitHub Docs](https://docs.github.com/en)
- [Basic syntax of markdown](https://www.markdownguide.org/basic-syntax/)
- [GitHub markdown syntax](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax)
- [Creating an issue](https://docs.github.com/en/issues/tracking-your-work-with-issues/creating-an-issue)
- [Creating a pull request from a fork](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-a-pull-request-from-a-fork)
