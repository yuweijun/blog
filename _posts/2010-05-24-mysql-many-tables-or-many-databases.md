---
layout: post
title: "mysql: many tables or many databases?"
date: "Mon May 24 2010 10:48:00 GMT+0800 (CST)"
categories: mysql
---

Question
-----

For a project we having a bunch of data that always have the same structure and is not linked together. There are two approaches to save the data:

* Creating a new database for every pool (about 15-25 tables)
* Creating all the tables in one database and differ the pools by table names.

Which one is easier and faster to handle for MySQL?

Answer
-----

There should be no significant performance difference between multiple tables in a single database versus multiple tables in separate databases.

In MySQL, databases (standard SQL uses the term "schema" for this) serve chiefly as a namespace for tables. A database has only a few attributes, e.g. the default character set and collation. And that usage of GRANT makes it convenient to control access privileges per database, but that has nothing to do with performance.

You can access tables in any database from a single connection (provided they are managed by the same instance of MySQL Server). You just have to qualify the table name:

{% highlight sql %}
SELECT * FROM database17.accounts_table;
{% endhighlight %}

This is purely a syntactical difference. It should have no effect on performance.

Regarding storage, you can't organize tables into a file-per-database as @Chris speculates. With the MyISAM storage engine, you always have a file per table. With the InnoDB storage engine, you either have a single set of storage files that amalgamate all tables, or else you have a file per table (this is configured for the whole MySQL server, not per database). In either case, there's no performance advantage or disadvantage to creating the tables in a single database versus many databases.

There aren't many MySQL configuration parameters that work per database. Most parameters that affect server performance are server-wide in scope.

Regarding backups, you can specify a subset of tables as arguments to the mysqldump command. It may be more convenient to back up logical sets of tables per database, without having to name all the tables on the command-line. But it should make no difference to performance, only convenience for you as you enter the backup command.

-- Bill Karwin (the author of [SQL Antipatterns](http://www.pragprog.com/titles/bksqla/sql-antipatterns) from Pragmatic Bookshelf)
