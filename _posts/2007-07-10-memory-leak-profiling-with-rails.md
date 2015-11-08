---
layout: post
title: "memory leak profiling with rails"
date: "Tue Jul 10 2007 15:36:00 GMT+0800 (CST)"
categories: ruby
---

Ruby on Rails项目中有内存溢出，总是需要定期重启mongrel_cluster，在网上找了一个程序，用于检查ruby内存中的对象情况，以判断程序内存溢出的可能原因：

{% highlight ruby %}
# author: scott@sigkill.org
# http://scottstuff.net/blog/articles/2006/08/17/memory-leak-profiling-with-rails
class MemoryProfiler

  DEFAULTS = {:delay => 10, :string_debug => false}

  def self.start(opt = {})
    opt = DEFAULTS.dup.merge(opt)

    Thread.new do
      prev = Hash.new(0)
      curr = Hash.new(0)
      curr_strings = []
      delta = Hash.new(0)

      file = File.open('log/memory_profiler.log', 'w')

      loop do |i|
        begin
          GC.start
          curr.clear
          curr_strings = [] if opt[:string_debug]

          ObjectSpace.each_object do |o|
            curr[o.class] += 1 # Marshal.dump(o).size rescue 1
            if opt[:string_debug] and o.class == String
              curr_strings.push o
            end
          end

          if opt[:string_debug]
            File.open("log/memory_profiler_strings#{Time.now.to_i}.log", 'w') do |f|
              curr_strings.sort.each do |s|
                f.puts s
              end
            end
            curr_strings.clear
          end

          delta.clear
          (curr.keys + delta.keys).uniq.each do |k,v|
            delta[k] = curr[k] - prev[k]
          end

          file.puts "Top 20"
          delta.sort_by { |k, v| -v.abs }[0..19].sort_by { |k, v| -v}.each do |k, v|
            file.printf "%+5d: %s (%d)\n", v, k.name, curr[k] unless v == 0
          end
          file.flush

          delta.clear
          prev.clear
          prev.update curr
          GC.start
        rescue Exception => err
          STDERR.puts "** memory_profiler error: #{err}"
        end

        sleep opt[:delay]
      end

    end # end loop

  end # end Thread.new

end # end def self.start

{% endhighlight %}
