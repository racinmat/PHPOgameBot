Ogame Bot
=============

This is bot for browser game [Ogame](www.ogame.cz), based on PHP, Nette Framework, and most importantly, Selenium and Codedeption.
Uses persistent queue and scheduler to plan and execute tasks when their preconditions are met.
Provies also web-based interface for managing the queue.

Queue with tasks for bot can be filled in web gui or by modifying the queue.json file.

## Setup

- Copy `app/config/config.local.neon.dist` to `app/config/config.local.neon` and fill credentials there.
 Slack token is needed because the bot sends informatino about crash to slack.


## About
This bot is based on selenium server with chrome driver. It uses Codeception library as its browser client. 
The app is written in Nette Framework and uses Symfony commands to start the bot from CLI.

The app contains 2 parts:
 - Web based administration to add, manage and delete commands, which are stored in JSON files.
 - Bot itself, which uses Codeception and Chrome browser, reads commands from JSON and executes them.

Basically, there are two types of commands: 
 - Basic commands, which are executed once, stored in `www/queue.json`. They are removed from queue after successful execution.
 - Repetitive commands, which are executed every run, after all basic commands are either executed, 
 or there is some command which can not be executed (not enough resources, ships...), stored in `www/repetitive.json`.
 Repetitive commands are not removed from queue after successful execution.  

The bot works as follows:
 - It opens browser and logs you in.
 - Is started by either `php php www/index.php bot:queue` or `php www/index.php bot:queue --debug-mode`
 	- `www/index.php` is entrypoint in Nette Framework. This command starts Symfony command `app/commands/ProcessQueueCommand.php`.
 - It checks whether there is incoming attack, and in case of attack, it tries to take whole fleet, fill it with all resources (hardcoded, high number),
  and sends it to first colony it sees.
 - It loads basic commands, and tries to execute them one by one, in same order as they are in JSON. 
 - When some command can not be executed (there is not enough resources, ships, building preconditions are not met...) 
 	or when all basic commands are executed, it starts processing repetitive commands. 
 	E.G.: When 10 commands are in queue, and 6. can not be executed, 6.-10. remain in queue. Bot does not even try to execute 7.-10. command.  
 - When some repetitive command can not be executed, it is skipped and other repetitive command starts being executed.
 	E.G.: When there are commands for every colony sending resources to base planet, and 1. colony does not have enough resources, 
 	it is skipped and other colonies try to execute sending from other colonies.
 - Again, it checks whether there is incoming attack and if so, it tries to send resources and fleet away.
 - It calculates time of next run by following rules:
 	- It looks at the failed command (if there is any), and repetitive commands, and tries to calculate earliest run 
 	when it can execute some of them. Based on free fleet slot if it wants to send fleet and all slots are full, or 
 	when resources will be mined when there is not enough resources...
 	- It sets next run as min(this calculated time, some random time in 5 minute interval - because of possible attacks).
 	- It saves this time to `www/cron.txt`.
 - It logs out and closes browser.
 - It ends.
 
There is script for automating that process, `cron.php`, which is started just by `php cron.php`.
 - It has forever loop with following steps:
 	- It waits one minute 
	- It checks internet connection
 	- It reads time of next run of bot
 	- If time has come, it starts the bot

So waits and when there is time to run the bot, it runs it.

Commands are represented by classes in `app/model/queue/command`. For every command, there is processor which can process this command.
There is `app/model/queue/CommandDispatcher.php`, which just holds all processors and sends the command to processor which can process it.
All commands implement `app/model/queue/command/ICommand.php` interface.
All command processors implement `app/model/queue/ICommandProcessor.php` interface.

For `app/model/queue/command/IEnhanceCommand.php` there is preprocessor (the only implementation of `app/model/queue/ICommandPreProcessor.php` interface), 
which checks if storages are big enough for enhancing (common word both building and upgrading), and if not, it calculates how big storages are needed, 
and prepends commands to upgrade storages until there is enough capacity, before that command. 

## TODO

- add command read players score. Add showing of players who have big difference between total (or economic) score and military score - potentional farms
- skip repetitious probing when too few slots is available
- refactor buildings, ships and defense in to embeddable. And refactor researches too.
- fleetsave from main planet. Fleetsave only in pauses between two enhancements. Fleetsave only resources that are not needed
- consider using only FacebookWebDriver and use modified CodeceptionWebDriver for syntax sugar. No asserts, only returing values and elements
- fix creating new profiles in selenium webdriver (it takes too much space in disc)
- maybe bugfix: when parsing time to complete upgrade, the real time is few seconds later than parsed time
- maybe add feature to upgrade building (except of research lab) and research at the same time
	- maybe save to planet (not to planet, but to service with cache) what is currently being enhanced so it will not have to be read more times during the queue processing (must be invalidated, maybe use cache for that. Service will save it, not entity)
- add package buying to periodical commands
- refactor the game namespace. Use pageobjects to interact with pages and segregate logic of webdriver manipulation and game logic.
- add transport and deployment reading of incoming resources when calculating resources for enhancement
- add 'important' option for enhancing. All important enhancements will be privileged and resources will be send to them from other planets.
- when some planet does not have any enhancement in queue, it will send automatically resources to another planet which has enhancement in queue, even if it is not privileged. It will send to first non-processed enhancement in queue.
- bugfix: when upgrading hangar, fleet and defense should not be built. When upgrading research lab, researches should not be upgraded. Now it does not matter.
- during the galaxy scanning, delete abandoned planets which were there, but they are not anymore
- add storages full of resources checking
- maybe think about setting values and last visited and make it more transaction-like and domain-driven (last visited will be set automatically in setter, one setter for all resources....)
- add checking whether command was really done (reading build/upgrade status, checking fleet status...)
- read mines percentage settings
- calculate production based on percentage settings and lack of energy
- maybe export/import queue and repetitious commands
- think about routines implementation (repetitious commands)
- automatically buy new probes when probes are destroyed when gathering data and buy new satellites when destroyed (Maintain quantity in Ogame Automizer)
- repetitious tasks
	- set repeating frequency
	- farming scanned players 
		- save planet status (from Ogame Automizer - Attack Status)
		- set how many minimal resources to gather
		- set maximum deuterium consumption
		- set expected resourced ratio
		- predict resources on planet
		- maybe implement "výhodnost (attack priority)" from Ogame Automizer Hunter 
		- set how many fleet slots to use for farming and debris recycling (or how many slots to be reserved and let free)
	- finding best attack fleet from current ships to attack player with lots of resources
		- integrate console optifleet
		- save simulation results
		- advice which ships to build
	- gathering debris by recyclers
- automatic fleetsave on attack
	- building transporters when too many resources is on the planet
	- sending transporters from other planets to attacked planet to save rsources when it is time
	- set time to leave before attack
- maybe try to integrate Ogame Automizer constuctor for mines on planet optimization
- maybe try to implement generating construction list from Ogame Automizer
- randomize intervals, set how slow or big should be waiting betewwn actions (slider more bot - more human)
- setting automating resources sending and automatic building (e.g. for satellites for Graviton technology)
- when enhancement and batch probing are in queue, process the probing first, so enhancement is right before repetitive resurces transportation
	- this is must have because long pause between enhancing and repetitious resources transport sends resources intended for enhancement back.
- propably do not send resources from planet when enhancement is in queue in that planet
- add flying time to counting of resources of farms and amount of cargoes to send
- for batch fleet sending (probing and farm attacking), set the free slots limit when the command will be processed (will be used for isProcessingAvailable and getTimeToProcessingAvailable)
	- the limit can be possibly calculated as a fraction of the limit of ships in command
- add better invalidation for flights. Compare current count of flights and loaded count of flights. When they differ, invalidate. Detect attack by red triangle and then invalidate.
	- do not invalidate every 3 minutes, just when it is needed by comparing loaded and current count of flights. Remove past flights from loaded before comparing.
	- probably use id to identify flights, save this id to a flight and reload only flights with new ids. That should really speed up the whole process. 
- do not wait for fleet sending when not enough ships is present during batch fleet sending
- add reading reports command. Save last report read datetime to planet, next to lastVisited. When reading some older report, skip it.
- add logging of current action to cache - show that in dashboard (waiting for free fleet, reading research, etc...)
- detect not loaded css and refresh page if that is detected
- add graphs
 	- player position and buildings/resources level
 	- estimated resources and last visited time
 	- logging of many data and showing change of these data in time
- logging of farmed resources - to tune best time interval
- log planets switching from 'got all information' to 'did not get all information'
