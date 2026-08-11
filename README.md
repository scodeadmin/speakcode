## **Speak Code**

SpeakCode - Let's create something useful.

## **Do you speak code?**

I started this repo in 2021 around a simple idea: developers reuse code. Maintenance, new features, upgrades, monitoring. Very little of it gets written from nothing. The hard part back then was finding usable code and knowing whether it fit.

Five years later the finding part is mostly solved. Ask a model, get working code in seconds. The knowing part is not solved, and I do not think it is close.

That gap is what this repo is about now.

### **What changed**

Generation is cheap. A model will produce a function, a class, an entire service, and most of the time it runs. What a model cannot do is know your codebase.

* It does not know why that service was written the way it was.
* It does not know which requirement the change is tied to.
* It does not know what your team agreed to three sprints ago.
* It does not know what breaks downstream.

That is context, and context is the work. The judgment about whether a change belongs is the part that did not get automated.

### **Examples of code that gets reused**

**Microservices** (single-function modules with well-defined interfaces and operations).

```java
/* HelloWorld.java
 */

public class HelloWorld
{
	public static void main(String[] args) {
		System.out.println("Hello World!");
	}
}
```

**Web APIs** and other web-related components. The 2021 version of this file used `XMLHttpRequest`. Most code written today uses `fetch`, and a model will hand you either one depending on what it saw in training. Knowing which belongs in your codebase is on you.

```javascript
const res = await fetch("https://api.example.com/products/3");
const data = await res.json();
console.log(data);
```

**Object-oriented programming (OOP)** organizes software design around data, or objects, rather than functions and logic.

**Modular programming** separates functionality into independent, interchangeable modules, where each contains everything necessary to execute one aspect of the desired functionality.

```python
def greet(name):
    """
    This function greets to
    the person passed in as
    a parameter
    """
    print("Hello, " + name + ". Good morning!")
```

### **Why open source matters here**

Open source is the reason any of this works. The libraries, the runtimes, the package managers, the models themselves in many cases. Most of it is maintained by people who are not paid to do it.

There is a difference between using open source and participating in it.

* **Using** is installing the package and moving on.
* **Participating** is filing the issue, writing the doc, reviewing the PR, showing up to the working group.

Participating is also where the standards get set. Formats, protocols, and specifications are written by whoever shows up to write them. If you build on top of open source and never contribute back, someone else decides what that foundation looks like.

Documentation counts. Filing a clear issue counts. You do not have to ship a feature to be useful.

### **Tips for building your code library**

* Choose a consistent method for storing and editing modified code. GitHub is an excellent example of retaining code ownership, history, and change.
* If using a template, maintain the framework and save copies separately for changes.
* Research templates and APIs for usability and accessibility.
* Well-documented code and use case.
* Keep the reasoning, not just the code. Six months from now the code will still be there and the reason will not.

### **Challenges?**

* Limited knowledge or access to usable code.
* New role or job with limited experience.
* Code base changes and a new language is used.
* Generated code that runs but nobody can explain.
* Review load that grows faster than the team.

In 2021 developers spent hours looking through blogs and forums for usable code. Now they spend that time deciding whether generated code belongs. The bottleneck moved.

### **Where this goes**

SpeakCode is where I write down what I am learning about how developers actually work with code. It is not a product and it is not finished. It gets updated when I have something worth saying.

Contributions are open. See [learning-path.md](./learning-path.md) if you want to add your own path into tech.
