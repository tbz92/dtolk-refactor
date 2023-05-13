1. ### Overview
I spent almost 3.5 hours to examine and refactor the task and tried to cover as many things as possible. I believe there will be  many things
that can also be fixed/optimised. Most of the areas where same or similar things are happening I didn't change if because it was already covered earlier.
Try to work on new code blocks and below are my key points and takeaways in details.

2. ### Commit
Committed the code as per the instructions. Original code in initial commit and refactored in the next one. 
Tried to optimize and increase readability while refactoring.

3. ### IDE
I use PhpStorm as my IDE and use PSR-12 option for formatting the code. So it handles most of the formatting by itself.
But I try to write the code similar way that will look after formatting.

4. ### Assessment / Opinion
There are many thing that I myself try to focus and provide attention while developing the code.
Some of them are not present in the original code

1. <h6>Variable Naming</h6>
I prefer camel-case naming convention. But any convention can be used as far as it's consistent
throughout the application.

2. <h6>No uses of Form Requests</h6>
Form requests can be used to handled all the validation which helps the controller and repository clean and 
focus on just performing the action they are meant to do. Like creating/updating method should only create/update
instead of doing all the validation before sending the data to database.

3. <h6>Response pattern / Json Resources</h6>
I think the API handling can be done in much organized and structured way that follows the REST standards.
response() is capable of returning json response but will do additional step, instead we can use response()->json() which
will directly send the data and relevant status codes can be sent too. One way is to make use JsonResource and 
which came in quite handy. for example return new BookingResource($data). I also added that as sample file in my commit.

4. <h6>Short function / Single Responsibility</h6>
The functions should be short and obey the S (single responsibility) for SOLID principles. The function length
should be decent, so if you read through it, you shouldn't lose track of what were you looking for.

5. <h6>Methods Names</h6>
The method name should be descriptive, so you should know what a function does without seeing actual implementation.
So, if there is a method which returns a list that converts string to array. It should be named something like
convertStringToArray() instead of returnArray(). Here in first example we know that string is converted to array.

6. <h6>Repetition / Redundant code</h6>
I came across the code blocks that are redundant and can be moved to a function. I follow that 
if some logic/code is required to written more than once, then it should be moved to a function.

7. <h6>Environment variables</h6>
env() return the environment variables that are present in .env files. It is recommended to use env() always inside the config files.
The reason is we use caching on production and after that using env() function can result in a null value which can break the code.
So, best practice is to keep them inside a config files organized and use then using config().

8. <h6>Else Blocks</h6>
Unnecessary use of else blocks are used. Trying to avoid else in code makes it look way cleaner. 
I've removed from many places but there are still many places left, that can be made simpler. 

9. <h6>Eloquent</h6>
   1. Eloquent can be used in much better way. I didn't know the model but most of the things can be moved to model level.
   Scopes or accessor/mutators can be used. In code there are many places where the assignment is being done. These multiple lines can be cut short
   to single line just by defining the accessor/mutators.
   2. There is a difference in get() and first(). If the first record is required then we can just use first() instead of
   getting all records and getting first. Here $job->user()->first() is faster instead of $job->user()->get()->first()
   
10. <h6>Exception Handling / Custom Exceptions / Try / catch</h6>
I find that exception handling is missing and would need to be in place for correct error handling. Custom exceptions can also be made where necessary.
Same goes for try-catch block as well.

11. <h6>Transactions</h6>
Transactions should be used while creating/manipulating data.
