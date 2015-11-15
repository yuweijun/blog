---
layout: post
title: "mongodb tutorial and references"
date: "Tue Jul 06 2010 15:17:00 GMT+0800 (CST)"
categories: linux
---

Introduction
-----

MongoDB is a collection-oriented, schema-free document database.

By collection-oriented, we mean that data is grouped into sets that are called 'collections'. Each collection has a unique name in the database, and can contain an unlimited number of documents. Collections are analogous to tables in a RDBMS, except that they don't have any defined schema.

By schema-free, we mean that the database doesn't need to know anything about the structure of the documents that you store in a collection. In fact, you can store documents with different structure in the same collection if you so choose.

By document, we mean that we store data that is a structured collection of key-value pairs, where keys are strings, and values are any of a rich set of data types, including arrays and documents. We call this data format "BSON" for "Binary Serialized dOcument Notation."

MongoDB is a server process that runs on Linux, Windows and OS X. It can be run both as a 32 or 64-bit application. We recommend running in 64-bit mode, since Mongo is limited to a total data size of about 2GB for all databases in 32-bit mode.

The MongoDB process listens on port 27017 by default (note that this can be set at start time - please see Command Line Parameters for more information).

MongoDB stores its data in files (default location is /data/db/), and uses memory mapped files for data management for efficiency.

Example data of mongodb

{% highlight javascript %}
$> bin/mongo --shell slides.js

/*
* Slides for presentation on Mastering the MongoDB Shell.
* Copyright 2010 Mike Dirolf (http://dirolf.com)
* This work is licensed under the Creative Commons
* Attribution-Noncommercial-Share Alike 3.0 United States License. To
* view a copy of this license, visit
* http://creativecommons.org/licenses/by-nc-sa/3.0/us/ or send a
* letter to Creative Commons, 171 Second Street, Suite 300, San
* Francisco, California, 94105, USA.
* Originally given at MongoSF on 4/30/2010.
* Modified and given at MongoNYC on 5/21/2010.
* To use: run `mongo --shell slides.js`
*/
db = db.getSisterDB("shell");
db.dropDatabase();
// some sample data
for (var i = 0; i < 1000; i += 1) {
    db.data.save({
        x: i
    });
} // "slides"
db.deck.save({
    slide: 0,
    welcome: "to MongoNYC!",
    hashtag: "#mongonyc",
    mirror: "http://confmirror.10gen.com/"
});
db.deck.save({
    slide: 1,
    title: "Mastering the MongoDB Shell",
    who: "Mike Dirolf, 10gen",
    handle: "@mdirolf"
});
db.deck.save({
    slide: 2,
    question: "what is the shell?",
    answer: "a better Powerpoint?"
});
db.deck.save({
    slide: 3,
    question: "what is the shell?",
    answer: "a full JavaScript environment"
});
db.deck.save({
    slide: 4,
    question: "what is the shell?",
    answer: "a reference MongoDB client"
});
db.deck.save({
    slide: 5,
    "use cases": ["administrative scripting", "exploring and debugging", "learning (and teaching!)"]
});
db.deck.save({
    slide: 6,
    repl: ["arrows for history", "^L"]
});
db.deck.save({
    slide: 7,
    "getting help": ["help", "db.help", "db.foo.help"]
});
db.deck.save({
    slide: 8,
    "show": ["dbs", "collections", "users", "profile"]
});
db.deck.save({
    slide: 9,
    navigating: "databases",
    how: "'use' or 'db.getSisterDB'"
});
db.deck.save({
    slide: 10,
    navigating: "collections",
    how: "dots, brackets, or 'db.getCollection'",
    note: "careful with names like foo-bar"
});
db.deck.save({
    slide: 11,
    "basic operations": ["insert", "findOne", "find", "remove"]
});
db.deck.save({
    slide: 12,
    "fun with cursors": ["auto-iteration", "it"]
});
db.deck.save({
    slide: 13,
    "error checking": "auto 'db.getLastError'"
});
db.deck.save({
    slide: 14,
    "commands": ["count", "stats", "repairDatabase"],
    "meta": "listCommands"
});
db.deck.save({
    slide: 15,
    "pro tip!": "viewing JS source"
});
db.deck.save({
    slide: 16,
    "getting help": "--help"
});
db.deck.save({
    slide: 17,
    scripting: "run .js files",
    tools: ["--eval", "--shell", "runProgram"]
});
db.deck.save({
    slide: 18,
    warning: "dates in JS suck"
});
db.deck.save({
    slide: 19,
    warning: "array iteration in JS sucks"
});
db.deck.save({
    slide: 20,
    warning: "numeric types in JS suck"
});
db.deck.save({
    slide: 21,
    so: "why JS?"
});
db.deck.save({
    slide: 22,
    homework: ["convince 2 friends to try MongoDB", "send feedback @mdirolf"]
});
db.deck.save({
    slide: 23,
    url: "github.com/mdirolf/shell_presentation",
    questions: "?"
}); // current slide
var current = 0; // print current slide and advance
var next = function() {
    var slide = db.deck.findOne({
        slide: current
    });
    if (slide) {
        current++;
        delete slide._id;
        delete slide.slide;
        print(tojson(slide, null, false));
    } else {
        print("The End!");
    }
}; // go to slide and print
var go = function(n) {
    current = n;
    next();
}; // repeat the previous slide
var again = function() {
    current--;
    next();
};
{% endhighlight %}

Basic Commands of mongodb
-----

* [http://www.mongodb.org/display/DOCS/List+of+Database+Commands](http://www.mongodb.org/display/DOCS/List+of+Database+Commands)
* [http://rezmuh.sixceedinc.com/2010/02/basic-commands-to-get-you-started-with-mongodb.html](http://rezmuh.sixceedinc.com/2010/02/basic-commands-to-get-you-started-with-mongodb.html)

Privileged Commands
-----

Certain operations are for the database administrator only. These privileged operations may only be performed on the special database named admin.

Start the server like this:

{% highlight bash %}
$> bin/mongod --dbpath /path/to/data
{% endhighlight %}

Stop it with `Ctrl-C` or with kill (but don’t use `kill -9`, which doesn’t give the server a chance to shut down cleanly and flush data to disk).

Viewing stats without the mongo console:

Visit `http://localhost:28017` (28017 is the port the server is running on, plus 1000) to get an overview of what’s going on.

You can also query `http://localhost:28017/_status` to see the same data that `db.serverStatus()` returns, but in JSON format.

Use client command console
-----

{% highlight bash %}
$> bin/mongo
{% endhighlight %}

> use admin;
> db.addUser('dummy, 'dummy') // create new user with password "dummy"
> db.runCommand("shutdown"); // shut down the database

{% highlight bash %}
$> bin/mongo -u dummy -p dummy admin
{% endhighlight %}

Create a new Database:
-----

> use new_database
> db.addUser('dummy', 'differentpassword')

{% highlight bash %}
$> bin/mongo -u dummy -p differentpassword new_database
{% endhighlight %}

Deleting a database
-----

> use [db name]
> db.dropDatabase()

Working with Collections (Tables):
-----

> show collections

Lists all the available databases:
-----

> show dbs

To get a list of data in a specific collection, use:
-----

> db.[collection name].find()

Directory Global functions and properties:
-----

> for(var o in this) {
>     print(o);
> }

{% highlight text %}
__quiet
chatty
friendlyEqual
doassert
assert
argumentsToArray
isString
isNumber
isObject
tojson
tojsonObject
shellPrint
printjson
shellPrintHelper
shellHelper
help
Random
killWithUris
Geo
connect
MR
MapReduceResult
__lastres__
sleep_
sleep
quit_
quit
getMemInfo_
getMemInfo
_srand_
_srand
_rand_
_rand
_isWindows_
_isWindows
_startMongoProgram_
_startMongoProgram
runProgram_
runProgram
runMongoProgram_
runMongoProgram
stopMongod_
stopMongod
stopMongoProgram_
stopMongoProgram
stopMongoProgramByPid_
stopMongoProgramByPid
rawMongoProgramOutput_
rawMongoProgramOutput
clearRawMongoProgramOutput_
clearRawMongoProgramOutput
removeFile_
removeFile
listFiles_
listFiles
resetDbpath_
resetDbpath
copyDbpath_
copyDbpath
_parsePath
_parsePort
createMongoArgs
startMongodTest
startMongod
startMongodNoReset
startMongos
startMongoProgram
startMongoProgramNoConnect
myPort
ShardingTest
printShardingStatus
MongodRunner
ReplPair
ToolTest
ReplTest
allocatePorts
SyncCCTest
db
hex_md5_
hex_md5
version_
version
i
current
next
__iscmd__
___it___
it
{% endhighlight %}

Directory properties of db:
-----

> for(var o in db){
>     print(o);
> }

{% highlight text %}
_mongo
_name
shellPrint
getMongo
getSisterDB
getName
stats
getCollection
commandHelp
runCommand
_dbCommand
_adminCommand
addUser
removeUser
__pwHash
auth
createCollection
getProfilingLevel
dropDatabase
shutdownServer
cloneDatabase
cloneCollection
copyDatabase
repairDatabase
help
printCollectionStats
setProfilingLevel
eval
dbEval
groupeval
groupcmd
group
_groupFixParms
resetError
forceError
getLastError
getLastErrorObj
getLastErrorCmd
getPrevError
getCollectionNames
tojson
toString
currentOp
currentOP
killOp
killOP
getReplicationInfo
printReplicationInfo
printSlaveReplicationInfo
serverBuildInfo
serverStatus
version
printShardingStatus
{% endhighlight %}

> db.shell.help()

{% highlight text %}
DBCollection help
db.foo.count()
db.foo.dataSize()
db.foo.distinct( key ) - eg. db.foo.distinct( 'x' )
db.foo.drop() drop the collection
db.foo.dropIndex(name)
db.foo.dropIndexes()
db.foo.ensureIndex(keypattern,options) - options should be an object with these possible fields: name, unique, dropDups
db.foo.reIndex()
db.foo.find( [query] , [fields]) - first parameter is an optional query filter. second parameter is optional set of fields to return.
e.g. db.foo.find( { x : 77 } , { name : 1 , x : 1 } )
db.foo.find(...).count()
db.foo.find(...).limit(n)
db.foo.find(...).skip(n)
db.foo.find(...).sort(...)
db.foo.findOne([query])
db.foo.findAndModify( { update : ... , remove : bool [, query: {}, sort: {}, 'new': false] } )
db.foo.getDB() get DB object associated with collection
db.foo.getIndexes()
db.foo.group( { key : ..., initial: ..., reduce : ...[, cond: ...] } )
db.foo.mapReduce( mapFunction , reduceFunction , <optional params> )
db.foo.remove(query)
db.foo.renameCollection( newName , <droptarget> ) renames the collection.
db.foo.runCommand( name , <options> ) runs a db command with the given name where the 1st param is the colleciton name
db.foo.save(obj)
db.foo.stats()
db.foo.storageSize() - includes free space allocated to this collection
db.foo.totalIndexSize() - size in bytes of all the indexes
db.foo.totalSize() - storage allocated for all data and indexes
db.foo.update(query, object[, upsert_bool, multi_bool])
db.foo.validate() - SLOW
db.foo.getShardVersion() - only for use with sharding
{% endhighlight %}

> db.help()

{% highlight text %}
DB methods:
db.addUser(username, password[, readOnly=false])
db.auth(username, password)
db.cloneDatabase(fromhost)
db.commandHelp(name) returns the help for the command
db.copyDatabase(fromdb, todb, fromhost)
db.createCollection(name, { size : ..., capped : ..., max : ... } )
db.currentOp() displays the current operation in the db
db.dropDatabase()
db.eval(func, args) run code server-side
db.getCollection(cname) same as db['cname'] or db.cname
db.getCollectionNames()
db.getLastError() - just returns the err msg string
db.getLastErrorObj() - return full status object
db.getMongo() get the server connection object
db.getMongo().setSlaveOk() allow this connection to read from the nonmaster member of a replica pair
db.getName()
db.getPrevError()
db.getProfilingLevel()
db.getReplicationInfo()
db.getSisterDB(name) get the db at the same server as this onew
db.killOp(opid) kills the current operation in the db
db.printCollectionStats()
db.printReplicationInfo()
db.printSlaveReplicationInfo()
db.printShardingStatus()
db.removeUser(username)
db.repairDatabase()
db.resetError()
db.runCommand(cmdObj) run a database command.  if cmdObj is a string, turns it into { cmdObj : 1 }
db.serverStatus()
db.setProfilingLevel(level,) 0=off 1=slow 2=all
db.shutdownServer()
db.stats()
db.version() current version of the server
{% endhighlight %}

> help()

{% highlight text %}
HELP
show dbs                     show database names
show collections             show collections in current database
show users                   show users in current database
show profile                 show most recent system.profile entries with time >= 1ms
use <db name>                set curent database to <db name>
db.help()                    help on DB methods
db.foo.help()                help on collection methods
db.foo.find()                list objects in collection foo
db.foo.find( { a : 1 } )     list objects in foo where a == 1
it                           result of the last line evaluated; use to further iterate
{% endhighlight %}

If I insert the same Document twice, it does not raise an Error?

Using PyMongo for example, why does inserting the same document (read same _id) more than once not raise an error? When we need to detect if a document already exists in the database, we could try catching DuplicateKeyError on insert. Below we explicitly insert the document with the same _id twice but the exception is never raised. Why?

try
-----

{% highlight javascript %}
doc = { _id: '123123' }
db.foo.insert(doc)
db.foo.insert(doc)
except pymongo.errors.DuplicateKeyError, error:
print("Same _id inserted twice:", error)
{% endhighlight %}

The answer is that DuplicateKeyError will only be raised if we do the insert in safe mode i.e. db.foo.insert(doc, safe=True). The reason why we do not see an error raised with most drivers but with MongoDB's interactive shell is because the shell does a safe insert by default whereas, with most drivers, it is the developers choice whether or not to use a safe insert.

mongodb's interactive shell
-----

> doc = {_id: 123421, name: 'test'};
> // { "_id" : 123421, "name" : "test" }
> db.deck.insert(doc)
> db.deck.insert(doc)
> // E11000 duplicate key error index: shell.deck.$_id_  dup key: { : 123421.0 }

Backup
-----

1. [http://effectif.com/mongodb/mongo-administration](http://effectif.com/mongodb/mongo-administration)

There are basically two approaches to backing up a Mongo database:

1. mongodump and mongorestore are the classic approach. Dumps the contents of the database to files. The backup is stored in the same format as Mongo uses internally, so is very efficient. But it’s not a point-in-time snapshot.
2. To get a point-in-time snapshot, shut the database down, copy the disk files (e.g. with cp) and then start mongod up again.

Alternatively, rather than shutting mongod down before making your point-in-time snapshot, you could just stop it from accepting writes:

> db._adminCommand({fsync: 1, lock: 1})

{% highlight javascript %}
{
    "info" : "now locked against writes, use db.$cmd.sys.unlock.findOne() to unlock",
    "ok" : 1
}
{% endhighlight %}

To unlock the database again, you need to switch to the admin database and then unlock it:

> use admin
> // switched to db admin
> db.$cmd.sys.unlock.findOne()
> // { "ok" : 1, "info" : "unlock requested" }


If you don’t switch to the admin database first you’ll get an unauthorized error:

> db._adminCommand({fsync: 1, lock: 1})

{% highlight javascript %}
{
    "info" : "now locked against writes, use db.$cmd.sys.unlock.findOne() to unlock",
    "ok" : 1
}
{% endhighlight %}

> db.$cmd.sys.unlock.findOne()
> // { "err" : "unauthorized" }

You can take a point in time snapshot from a slave just as easily as your master database, which avoids downtime. This is one of the reasons that running a slave is so strongly recommended…

What RAID should I use?
-----

1. [http://www.mongodb.org/display/DOCS/Developer+FAQ](http://www.mongodb.org/display/DOCS/Developer+FAQ)

We recommend not using RAID-5, but rather, RAID-10 or the like. Both will work of course.

Replication
-----

Do it (did you read the previous section?). Seriously.

Start your master and slave up like this:

{% highlight bash %}
$> mongod --master --oplogSize 500
$> mongod --slave --source localhost:27017 --port 3000 --dbpath /data/slave
{% endhighlight %}

When seeding a new slave server from master use the `--fastsync` option.

You can see what’s going on with these two commands:

> db.printReplicationInfo()  # tells you how long your oplog will last
> db.printSlaveReplicationInfo()  # tells you how far behind the slave is

If the slave isn’t keeping up, how do you find out what’s going on? Check the mongo log for any recent errors. Try connecting with the mongo console. Try running queries from the console to see if everything is working. Run the status commands above to try and find out which database is taking up resources. If you can’t work it out hop on the IRC channel; Mathias says they’ll be very responsive.

What is the difference between MongoDB and RDBMSs
-----

1. [http://sunoano.name/ws/mongodb.html#faqs](http://sunoano.name/ws/mongodb.html#faqs)

mysql, postgresql, ...
----------------------

{% highlight text %}
Server:Port
- Database
- Table
- Row
{% endhighlight %}

MongoDB
--------

{% highlight text %}
Server:Port
- Database
- Collection
- Document
{% endhighlight %}

The concept of server and database are very similar. But the concept of table and collection are quite different. In RDBMSs a table is a rectangle. It is all columns and rows. Each row has a fixed number of columns, if we add a new column, we add that column to each and every row.

In MongoDB a collection is more like a really big box and each document is like a little bag of stuff in that box. Each bag contains whatever it needs in a totally flexible manner (read schema-less). However, schema-less does not equal type-less i.e. it is just that any document has its own schema, which it may or may not share with any other document. In practice it is normal to have the same schema for all the documents in collection.

Clone Database
-----

1. [http://www.mongodb.org/display/DOCS/Clone+Database](http://www.mongodb.org/display/DOCS/Clone+Database)

MongoDB includes commands for copying a database from one server to another.

{% highlight javascript %}
// copy an entire database from one name on one server to another
// name on another server.  omit <from_hostname> to copy from one
// name to another on the same server.
db.copyDatabase(<from_dbname>, <to_dbname>, <from_hostname>);
// if you must authenticate with the source database
db.copyDatabase(<from_dbname>, <to_dbname>, <from_hostname>, <username>, <password>);
// in "command" syntax (runnable from any driver):
db.runCommand( { copydb : 1, fromdb : ..., todb : ..., fromhost : ... } );
// command syntax for authenticating with the source:
n = db.runCommand( { copydbgetnonce : 1, fromhost: ... } );
db.runCommand( { copydb : 1, fromhost: ..., fromdb: ..., todb: ..., username: ..., nonce: n.nonce, key: <hash of username, nonce, password > } );

// clone the current database (implied by 'db') from another host
var fromhost = ...;
print("about to get a copy of database " + db + " from " + fromhost);
db.cloneDatabase(fromhost);
// in "command" syntax (runnable from any driver):
db.runCommand( { clone : fromhost } );
{% endhighlight %}

What might be a use case for --fork?
-----

The `--fork` switch forks a mongod process of another exiting process. One use case where this is pretty handy for example is if we SSH into a remote machine and start a MongoDB and leave right away after a new mongod got started.

The fastest way to do so would simply be to append /path/to/mongod --fork to the SSH command line since this will skip creating an intermediate shell and bring up a remote mongod right away.

Are there any Reasons not to use MongoDB?
-----

[http://sunoano.name/ws/mongodb.html#faqs](http://sunoano.name/ws/mongodb.html#faqs)

1. We need transactions (read ACID).
2. Our data is very relational.
3. Related to 2, we want to be able to do joins on the server (but can not do embedded objects / arrays).
4. We need triggers on our tables. There might be triggers available soon however.
5. Related to 4, we rely on triggers (or similar functionality) to do cascading updates. or deletes. As for #4, this issue probably goes away once triggers are available.
6. We need the database to enforce referential integrity (MongoDB has no notion of this at all).
7. If we need 100% per node durability.

Use Cases
-----

[http://www.mongodb.org/display/DOCS/Comparing+Mongo+DB+and+Couch+DB](http://www.mongodb.org/display/DOCS/Comparing+Mongo+DB+and+Couch+DB)

It may be helpful to look at some particular problems and consider how we could solve them.

1. if we were building Lotus Notes, we would use Couch as its programmer versioning reconciliation/MVCC model fits perfectly. Any problem where data is offline for hours then back online would fit this. In general, if we need several eventually consistent master-master replica databases, geographically distributed, often offline, we would use Couch.
1. if we had very high performance requirements we would use Mongo. For example, web site user profile object storage and caching of data from other sources.
1. if we were building a system with very critical transactions, such as bond trading, we would not use MongoDB for those transactions -- although we might in hybrid for other data elements of the system. For something like this we would likely choose a traditional RDBMS.
1. for a problem with very high update rates, we would use Mongo as it is good at that. For example, updating real time analytics counters for a web sites (pages views, visits, etc.)

Generally, we find MongoDB to be a very good fit for building web infrastructure.

ruby driver of mongodb tutorial
-----

1. [http://api.mongodb.org/ruby/1.0.3/index.html](http://api.mongodb.org/ruby/1.0.3/index.html)
1. [http://www.mongodb.org/display/DOCS/Ruby+Tutorial](http://www.mongodb.org/display/DOCS/Ruby+Tutorial)
1. [http://github.com/mongodb/mongo-ruby-driver](http://github.com/mongodb/mongo-ruby-driver)

{% highlight bash %}
$> gem update --system
$> gem install mongo
$> gem install bson
$> gem install bson_ext
{% endhighlight %}

Making a Connection
-----

{% highlight ruby %}
# db = Mongo::Connection.new.db("shell")
# db = Mongo::Connection.new("localhost").db("shell")
db = Mongo::Connection.new("localhost", 27017).db("shell")
{% endhighlight %}

Listing All Databases
-----

{% highlight ruby %}
m = Mongo::Connection.new # (optional host/port args)
m.database_names.each { |name| puts name }
m.database_info.each { |info| puts info.inspect}
{% endhighlight %}

Dropping a Database
-----

{% highlight ruby %}
m.drop_database('database_name')
{% endhighlight %}

Authentication (Optional)
-----

{% highlight ruby %}
auth = db.authenticate(my_user_name, my_password)
{% endhighlight %}

Getting a Collection
----

{% highlight ruby %}
deck = db.collection("deck")
{% endhighlight %}

Inserting a Document
-----

{% highlight ruby %}
deck = db['deck']

doc = {"name" => "MongoDB", "type" => "database", "count" => 1, "info" => {"x" => 203, "y" => '102'}}
deck.insert(doc)
100.times { |i| deck.insert("i" => i) }
{% endhighlight %}

Finding the First Document In a Collection using find_one()
-----

{% highlight ruby %}
my_doc = deck.find_one()
puts my_doc.inspect
{% endhighlight %}

Note the `_id` element has been added automatically by MongoDB to your document. Remember, MongoDB reserves element names that start with `_` for internal use.

Counting Documents in a Collection
-----

{% highlight ruby %}
puts deck.count()
{% endhighlight %}

Using a Cursor to get all of the Documents
-----

{% highlight ruby %}
deck.find.each { |row| puts row.inspect }
{% endhighlight %}

Getting a Single Document with a Query:
-----

{% highlight ruby %}
deck.find("i" => 71).each { |row| puts row.inspect }
{% endhighlight %}

Getting a Set of Documents With a Query:
-----

{% highlight ruby %}
deck.find("i" => {"$gt" => 50}).each { |row| puts row }
{% endhighlight %}

Querying with Regular Expressions:
-----

{% highlight ruby %}
deck.find("question" => /w/i).each { |row| puts row }
{% endhighlight %}

Creating An Index:
-----

To create an index, you specify an index name and an array of field names to be indexed, or a single field name.

{% highlight ruby %}
deck.create_index("i")
# deck.create_index([["i", Mongo::ASCENDING]])
{% endhighlight %}

Getting a List of Indexes on a Collection:
-----

{% highlight ruby %}
deck.index_information
{% endhighlight %}

Ruby driver example
-----

{% highlight ruby %}
require 'rubygems'  not necessary for Ruby 1.9
require 'mongo'
# 需要先导入slides.js之后，再进行下面的ruby代码测试
db = Mongo::Connection.new.db("mydb")
db = Mongo::Connection.new("localhost").db("mydb")
m = Mongo::Connection.new("localhost", 27017)
db = m.db("shell")
puts db.inspect
m.database_names.each { |name| puts name }
m.database_info.each { |info| puts info.inspect}
db.collection_names.each { |name| puts name }
deck = db['deck']
doc = {"name" => "MongoDB", "type" => "database", "count" => 1,
"info" => {"x" => 203, "y" => '102'}}
deck.insert(doc)
100.times { |i| deck.insert("i" => i) }
my_doc = deck.find_one()
puts my_doc.inspect
puts deck.count()
deck.find.each { |row| puts row.inspect }
deck.find("i" => 71).each { |row| puts row.inspect }
deck.find("i" => {"$gt" => 90}).each { |row| puts row }
deck.find("i" => {"$gt" => 20, "$lte" => 30}).each { |row| puts row }
deck.find("question" => /w/i).each { |row| puts row }
deck.create_index([["i", Mongo::ASCENDING]])
p deck.index_information
db.validate_collection('deck')
{% endhighlight %}

References
-----

1. [http://www.mongodb.org/display/DOCS/Developer+Zone](http://www.mongodb.org/display/DOCS/Developer+Zone)
