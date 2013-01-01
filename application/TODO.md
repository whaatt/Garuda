TODO
====

If you are interested in co-maintaining or just browsing, here are a few critical points.

* My code is commented, though not nearly as much as I would have liked, since this is production code.
* A writing statistics page is under consideration, but it might detract from a collaborative attitude.
* In general, I don't like my messy coding, but I am relatively pleased with the functionality as it is.

* Right now there's a lot of fudging going on between NULL and empty values.
	- I have no immediate plan to fix this.
	- I will fix this as is necessary for functionality.   
* No data-changing injections are possible, but ostensibly you could create a PHP or MYSQL error.
	- This is not high priority, because you have to __try__ to generate them.
	- The data revealed from these error messages is negligible for hackers.
* I did not use any MVC-type PHP frameworks, and the only framework I used overall was jQuery.
	- If I had more time and patience, I probably could have used Symfony or CakePHP or something.
	- A lot of the decision was motivated by the shiny front-end stuff that is not straightforward.