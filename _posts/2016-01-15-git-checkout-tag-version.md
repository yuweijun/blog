---
layout: post
title: "git checkout specified tag version"
date: "Fri, 15 Jan 2016 15:06:41 +0800"
categories: linux
---

## List tags

{% highlight bash %}
$> git tag
# pattern match
$> git tag -l "v1.8.5*"
{% endhighlight %}

## Creating an annotated tag

{% highlight bash %}
$> git tag -a v1.4 -m "my version 1.4"
{% endhighlight %}

## Creating lightweight tags

To create a lightweight tag, don’t supply the -a, -s, or -m option:

{% highlight bash %}
$> git tag v1.4-lw
{% endhighlight %}

## Sharing Tags

{% highlight bash %}
$> git push origin v1.5
{% endhighlight %}

## Checking out Tags

syntax: `git checkout -b [branchname] [tagname]`

{% highlight bash %}
$> git checkout -b new-branch-name v2.0.0
{% endhighlight %}

or using this syntax: `git checkout [tagname] -b [branchname]`

{% highlight bash %}
$> git checkout v2.0.0 -b new-branch-name
{% endhighlight %}


References
-----

1. [2.6 Git Basics - Tagging](https://git-scm.com/book/en/v2/Git-Basics-Tagging#Checking-out-Tags)
2. [Download a specific tag with Git](http://stackoverflow.com/questions/791959/download-a-specific-tag-with-git)
3. [git 检出标签](http://www.4e00.com/git-zh/1-git-basics.html#-K8t9fAIg)
