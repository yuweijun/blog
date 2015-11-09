---
layout: post
title: "vim folding commands"
date: "Thu Dec 20 2007 14:33:00 GMT+0800 (CST)"
categories: vim
---

more usage command of vim folding can get from this [page](http://www.linux.com/articles/114138).

|Command     |Operation  |
|:-----------|:----------|
|zf#j        |creates a fold from the cursor down # lines.|
|zf/string   |creates a fold from the cursor to string .|
|zj          |moves the cursor to the next fold.|
|zk          |moves the cursor to the previous fold.|
|zo          |opens a fold at the cursor.|
|zO          |opens all folds at the cursor.|
|zm          |increases the foldlevel by one.|
|zM          |closes all open folds.|
|zr          |decreases the foldlevel by one.|
|zR          |decreases the foldlevel to zero -- all folds will be open.|
|zd          |deletes the fold at the cursor.|
|zE          |deletes all folds.|
|[z          |move to start of open fold.|
|]z          |move to end of open fold.|
