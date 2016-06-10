Ogame Bot
=============

This is bot for browser game ogame. 
Queue with tasks for bot can be filled in web gui or by modifying the queue.json file.

TODO
-----
- add command read players score. Add showing of players who have big difference between total (or economic) score and military score - potentional farms
- add incoming resources to calculation of time to process some command which needs these resources
- refactor waitings in fleet sending to waitForText or something like that, to speed up probes sending. Maybe add parameter slow, which will enable additional random waiting
- add disable option for repetitious commands (or automatically disable sending resources away when some resource dependent commands are waiting to proceed)
- refactor buildings, ships and defense in to embeddable. And refactor researches too.
- fleetsave from main planet. Fleetsave only in pauses between two enhancements
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
	- sending transporters from other planets to save rsources when it is time
	- set time to leave before attack
- maybe try to integrate Ogame Automizer constuctor for mines on planet optimization
- maybe try to implement generating construction list from Ogame Automizer
- randomize intervals, set how slow or big should be waiting betewwn actions (slider more bot - more human)
- setting automating resources sending and automatic building (e.g. for satellites for Graviton technology)