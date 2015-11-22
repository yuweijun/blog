---
layout: post
title: "date输出rfc822格式的字符串"
date: "Sun, 22 Nov 2015 11:59:37 +0800"
categories: linux
---

linux
-----

{% highlight bash %}
$> date -R
# Sun, 22 Nov 2015 12:27:31 +0800

$> date "+%a, %d %b %Y %H:%M:%S %z"
# Sun, 22 Nov 2015 12:27:31 +0800

$> date +"%a, %d %b %Y %H:%M:%S %z"
# Sun, 22 Nov 2015 12:36:13 +0800
{% endhighlight %}

linux中date还有个`-I`的参数，可以很方便得到`ISO-8601`格式，也就是常用的`yyyy-MM-dd`形式的字符串。

{% highlight bash %}
$> date -I
# 2015-11-22
{% endhighlight %}

mac os x
-----

Mac OS中date命令与linux版本的参数不同，要得到`rfc822`格式，需要格式化日期对象。

格式化字符串前面需要有个`+`号，可以在双引号里面，也可以在外面，linux手册提示为`date [OPTION]... [+FORMAT]`。

{% highlight bash %}
$> date "+%a, %d %b %Y %H:%M:%S %z"
# Sun, 22 Nov 2015 12:27:31 +0800

$> date +"%a, %d %b %Y %H:%M:%S %z"
# Sun, 22 Nov 2015 12:27:31 +0800
{% endhighlight %}

perl
-----

{% highlight bash %}
$> perl -v
# This is perl 5, version 16, subversion 2 (v5.16.2) built for darwin-thread-multi-2level

$> perl -e 'use POSIX qw(strftime); print strftime("%a, %d %b %Y %H:%M:%S %z", localtime(time())) . "\n";'
# Sun, 22 Nov 2015 12:01:11 +0800
{% endhighlight %}

附linux date命令参数详细说明
-----

{% highlight text %}
date

date [options] [+format] [date]

Print the current date and time. You may specify a display format. format can consist of literal text strings (blanks must be quoted) as well as field descriptors, whose values will appear as described in the following entries (the listing shows some logical groupings). A privileged user can change the system's date and time.

Options

+format
Display current date in a nonstandard format. For example:

$date +"%A %j %n%k %p" Tuesday 248 15 PM

The default is %a %b %e %T %Z %Y (e.g., Tue Sep 5 14:59:37 EDT 2005).

-d date, --date date
Display date, which should be in quotes and may be in the format d days or m months d days, to print a date in the future. Specify ago to print a date in the past. You may include formatting (see the following section).

-f datefile, --file=datefile
Like -d, but printed once for each line of datefile.

-I [timespec] , --iso-8601[=timespec]
Display in ISO-8601 format. If specified, timespec can have one of the following values: date (for date only), hours, minutes, or seconds to get the indicated precision.

-r file, --reference=file
Display the time file was last modified.

-R, --rfc-822
Display the date in RFC 822 format.

--help
Print help message and exit.

--version
Print version information and exit.

-s date, --set date
Set the date.

-u, --universal
Set the date to Greenwich Mean Time, not local time.

Format

The exact result of many of these codes is locale-specific and depends upon your language setting, particularly the LANG environment variable. See locale.

%
Literal %.

- (hyphen)
Do not pad fields (default: pad fields with zeros).

_ (underscore)
Pad fields with space (default: zeros).

%a
Abbreviated weekday.

%b
Abbreviated month name.

%c
Country-specific date and time format.

%d
Day of month (01-31).

%h
Same as %b.

%j
Julian day of year (001-366).

%k
Hour in 24-hour format, without leading zeros (0-23).

%l
Hour in 12-hour format, without leading zeros (1-12).

%m
Month of year (01-12).

%n
Insert a new line.

%p
String to indicate a.m. or p.m.

%r
Time in %I:%M:%S %p (12-hour) format.

%s
Seconds since "the Epoch," which is 1970-01-01 00:00:00 UTC (a nonstandard extension).

%t
Insert a tab.

%w
Day of week (Sunday = 0).

%x
Country-specific date format based on locale.

%y
Last two digits of year (00-99).

%z
RFC 822-style numeric time zone.

%A
Full weekday.

%B
Full month name.

%D
Date in %m/%d/%y format.

%H
Hour in 24-hour format (00-23).

%I
Hour in 12-hour format (01-12).

%M
Minutes (00-59).

%S
Seconds (00-59).

%T
Time in %H:%M:%S format.

%U
Week number in year (00-53); start week on Sunday.

%V
Week number in year (01-52); start week on Monday.

%W
Week number in year (00-53); start week on Monday.

%X
Country-specific time format based on locale.

%Y
Four-digit year (e.g., 2006).

%Z
Time-zone name.

Strings for setting date

Strings for setting the date may be numeric or nonnumeric. Numeric strings consist of time, day, and year in the format MMDDhhmm[[CC] YY] [.ss] . Nonnumeric strings may include month strings, time zones, a.m., and p.m.

time
A two-digit hour and two-digit minute (hhmm); hh uses 24-hour format.

day
A two-digit month and two-digit day of month (MMDD); default is current day and month.

year
The year specified as either the full four-digit century and year or just the two-digit year; the default is the current year.

Examples

Set the date to July 1 (0701), 4 a.m. (0400), 2005 (05):

date 0701040095

The command:

date +"Hello%t Date is %D %n%t Time is %T"

produces a formatted date as follows:

Hello Date is 05/09/05 Time is 17:53:39
{% endhighlight %}

References
-----

1. [How do I elegantly print the date in RFC822 format in Perl?](http://stackoverflow.com/questions/172110/how-do-i-elegantly-print-the-date-in-rfc822-format-in-perl)
2. [Linux in a Nutshell, 5th Edition.](http://archive.oreilly.com/linux/cmd/cmd.csp?path=d/date)
3. [Date.prototype.toUTCString()](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/toUTCString)
