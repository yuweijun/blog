---
layout: post
title: "关于java的受检异常"
date: "Tue, 25 Sep 2012 11:32:52 +0800"
categories: java
---

java语言中的异常是一个Throwable对象，其下又分二个子类：Error，Exception，Exception又可以分为受检异常和运行时异常(非受检异常)，CheckedException/UncheckedException。

一般Error都是系统级别的错误，一般自定义异常都是继承自Exception，而不要去继承Error。

程序如果抛出Error，不用管它，让JVM去处理吧，如OutOfMemoryError。因此代码中不要出现catch Throwable的写法。

对于受检异常和非受检异常，java有不同的处理规则，如果调用方法签名中使用的是非受检异常，调用者可以不必在代码中捕获它，也不需要在方法签名中声明它。如果调用方法签名里使用了throws子句声明了受检异常，那调用者就被迫对此异常要进行捕获或者在自已的方法签名中声明它们。

编译器也会自动告诉你，调用方法中是否有抛出受检异常。

Effective Java作者Joshua Bloch的观点
-----

1. 只有在需要捕获异常的情况下才使用异常，不应该用于正常的控制流，优先使用标准的，易理解的模式，如下代码示例一。
2. 设计良好的API可以通过提供状态检测方法，来避免强迫客户端程序员为了正常的控制流而使用异常，如下代码示例二。
3. 对可恢复的情况使用受检异常，对编程错误使用运行时异常。(这个说法是正确的，而问题是在于有多少抛出来的受检异常可恢复？又恢复了多少？因此Bloch马上又提到了下面这条准则)。
4. 避免不必要地使用受检异常。这个在语言设计而言，是一个很好的特性，能强迫程序员处理异常，增强了可靠性，但会使API使用起来非常不方便，给程序员增添了不可忽视的负担。
5. 在使用到异常的地方，优先使用标准异常而不是自定义异常，这样会使你的代码更具可读性（因为标准异常大家都熟悉），同时也减少了异常类。
6. 每个抛出异常的方法都要有文档说明，说明抛出异常的条件，对于会抛出运行时异常的方法，可以在文档中描述方法被成功执行的前提条件和代码调用示例。
7. 提供足够多的异常信息，不要吞掉异常。
8. 保持异常的原子性，将对象恢复到调用之前的状态。
9. 不要忽略异常。

{% highlight java %}
// 代码示例一
// Horrible abuse of exceptions. Don't ever do this!
try {
    int i = 0;
    while(true)
    a[i++].f();
} catch(ArrayIndexOutOfBoundsException e) {
}

// Normal
for (int i = 0; i < a.length; i++) {
    a[i].f();
}
{% endhighlight %}

{% highlight java %}
// 代码示例二
// standard idiom for iterating over a collection:
for (Iterator i = collection.iterator(); i.hasNext(); ) {
    Foo foo = (Foo) i.next();
    ...
}
// If Iterator lacked the hasNext method, the client would be forced to do the following, instead:
// Do not use this hideous idiom for iteration over a collection!
try {
    Iterator i = collection.iterator();
    while(true) {
        Foo foo = (Foo) i.next();
        ...
    }
} catch (NoSuchElementException e) {
}
{% endhighlight %}

常见的标准异常有
-----

{% highlight text %}
IllegalArgumentException，这个异常表示调用者传递了不合适的参数。一般在检测到参数不正确的时候我们可以抛出这个异常，或者是返回null值或false，结束方法。
NullPointerException，这个是我们最熟悉的异常了。如果调用者在某个不允许null值的参数中传递了null值，习惯的做法就是抛出NullPointerException异常。
IndexOutOfBoundsException，如果调用者在某个序列下标的参数中传递了越界的值，应该抛出的就是IndexOutOfBoundsException异常。比如访问超过数组下标的数组元素。
ConcurrentModificationException，这个异常被设计在java.util包中，用来表示一个单线程的对象正在被并发的修改。
UnsupportedOperationException，这个异常表示当前对象不支持所请求的操作。比如在实现类中没有实现接口定义的方法，就会抛出这个异常。
NumberFormatException，这个异常表示数据格式有误，还有一个ArithmeticException异常，表示算术异常，比如在除法运算中传递了0作为除数。
{% endhighlight %}

Joshua Bloch这其实也是委婉的提出不要太自作聪明的写一堆的BusinessException，java现有提供的异常类型已经非常丰富，先全部熟悉后看是否已经有合适的现有异常类，而不要盲目去定义自已的异常类型。

think in java 作者Bruce Eckel的观点
-----

1. 首先java无谓的发明了"受检异常"
2. 在示例性质的代码和小程序来看，受检异常很不错，但是程序代码变多之后，就会在代码中添加大量的此类无用代码，增加了代码的维护难度。
3. 有些异常就算抛给程序员，他们也无法提供处理程序，强迫他们处理是不现实的，比如MD5算法生成的异常，Thread.sleep()方法，除了转化为RuntimeException或者捕获扔掉，没什么特别的办法。
4. 不在于让编译器强制程序员去处理这些受检异常，而是提供一致的错误报告。
5. 减少编译时施加的约束能显著提高程序员的编程效率。
6. 强调自动构建和单元测试，以保证程序的健壮性。
7. 要让java把受检异常从语言中去除，可能性看来很渺茫。
8. 异常通常被认为是一个工具，使得你可以在运行时报告错误并从错误中恢复，但是怀疑到底有多少这种"恢复"真正得以实现了，这种恢复概率极少。
9. java的受检异常会要求程序员把异常处理程序的代码文本附接到会引发异常的调用上，当一个代码块中大量出现try-catch时，这会降低程序的可读性，也使得程序的正常思路被异常处理给破坏了。

Martin Fowler的观点
-----

1. 总体来说，异常机制很不错，但是java中的"受检异常"带来的麻烦比好处要多。

Spring作者Rod Johnson的观点
-----

1. 可以通过spring中的代码文档来实际说明：`org.springframework.dao.DataAccessException`这个异常目的是为了让使用者更好的找到并处理所遇到的错误，而不需要了解特定的底层数据访问API细节，如JDBC。因为这是一个运行时异常，所以用户一般是不需要在方法中写代码来捕获这个异常，因为多数情况下抛出了这个异常都是极严重的问题，应中止代码执行。

综上关于受检异常的观点
-----

1. 除了在做框架程序，尽量不要在业务逻辑的代码中，再自定义受检异常，而使用JAVA已经提供的原生异常，提高代码可读性和维护性，不要让别的程序员在调用API时，还要理解并处理陌生的异常，或者是在调用者的方法签名上再抛出异常，严重影响代码可读性。
2. 对于像Thread.sleep()之类的尽量封装一下工具类，并通过"异常转换"，包装进RuntimeException，通过异常链抛出，保证你不会丢失任何的异常信息，如下异常捕获示例一。
3. 作为API设计者，只有明白API抛出来的异常，开发程序员能理解并提供处理程序，才合适抛出来，不然只会增加调用程序的负担，增加代码复杂性。可以直接在接口内部使用运行时异常抛出，如下异常捕获示例二。
4. 作为API调用者，只有在你知道如何处理异常的时候才捕获异常，不然请继续抛出，不要"吞"掉异常。而java的受检异常则会异致这个问题的复杂化，在程序员还没有准备处理错误的时候，被迫加上catch子句，并异致不应该的"异常吞食"。

{% highlight java %}
// 异常捕获示例一
try {
    // ... to do something useful
} catch (IDontKnowWhatToDoWithThisCheckedException e) {
    throw new RuntimeException(e);
}
{% endhighlight %}

{% highlight java %}
// 异常捕获示例二
try {
    // ... to do something useful
} catch(ObligatoryException e) {} // Gulp!
{% endhighlight %}

对比java和ruby文件操作的代码
-----

{% highlight java %}
public class InputFile {
    private BufferedReader in ;
    public InputFile(String fname) throws Exception {
        try { in = new BufferedReader(new FileReader(fname));
            // Other code that might throw exceptions
        } catch (FileNotFoundException e) {
            System.out.println("Could not open " + fname);
            // Wasn’t open, so don’t close it
            throw e;
        } catch (Exception e) {
            // All other exceptions must close it
            try { in.close();
            } catch (IOException e2) {
                System.out.println("in.close() unsuccessful");
            }
            throw e; // Rethrow
        } finally {
            // Don’t close it here!!!
        }
    }
    public String getLine() {
        String s;
        try {
            s = in.readLine();
        } catch (IOException e) {
            throw new RuntimeException("readLine() failed");
        }
        return s;
    }
    public void dispose() {
        try { in.close();
            System.out.println("dispose() successful");
        } catch (IOException e2) {
            throw new RuntimeException("in.close() failed");
        }
    }
} ///:~
{% endhighlight %}

ruby版本读取文件
-----

{% highlight ruby %}
File.foreach("test.html") { |line|
    puts line
} if File.exist? "test.html"
{% endhighlight %}
