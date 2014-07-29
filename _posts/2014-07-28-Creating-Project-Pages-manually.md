---
layout: post
title:  "Creating Project pages Manually!"
date:   2014-07-28 10:44:05
categories: jekyll update
---

Creating Project Pages manually
===============================

If you're familiar with command-line Git, it's straightforward to manually create a Project Pages site.

Make a fresh clone
------------------

To set up a Project Pages site, you need to create a new "orphan" branch (a branch that has no common history with an existing branch) in your repository. The safest way to do this is to start with a fresh clone:

    git clone https://github.com/user/repository.git
    # Clone our repository
    # Cloning into 'repository'...
    # remote: Counting objects: 2791, done.
    # remote: Compressing objects: 100% (1225/1225), done.
    # remote: Total 2791 (delta 1722), reused 2513 (delta 1493)
    # Receiving objects: 100% (2791/2791), 3.77 MiB | 969 KiB/s, done.
    # Resolving deltas: 100% (1722/1722), done.
    Create a gh-pages branch

Once you have a clean repository, you'll need to create the new gh-pages branch and remove all content from the working directory and index:

    cd repository

    git checkout --orphan gh-pages
    # Creates our branch, without any parents (it's an orphan!)
    # Switched to a new branch 'gh-pages'

    git rm -rf .
    # Remove all files from the old working tree
    # rm '.gitignore'

Tip: The gh-pages branch won't appear in the list of branches generated by git branch until you make your first commit.
Add content and push

In order to trigger a build when you push to your Page's repository, you must first verify your email address.
Now you have an empty working directory. You can create some content in this branch and push it to GitHub. For example:

    jekyll new .
    # generate jekyll structure and files

    git add -A
    git commit -a -m "jekyll init commit"
    git push origin gh-pages

Tip: After the first push, it can take up to ten minutes before your GitHub Pages site is available.
Load your new GitHub Pages site

After your push to the gh-pages branch, your Project Pages site will be available at username.github.io/projectname. Note that published Pages are always publicly visible, even if their repository is private. To set up a custom domain for GitHub Pages sites, see Setting up a custom domain with GitHub Pages.

Tip: Enterprise Project Pages sites will be served at a subdirectory (e.g. http(s)://[hostname]/pages/[user or organization name]/projectname) instead of a subdomain (e.g. http://[user or organization name].github.io/projectname)