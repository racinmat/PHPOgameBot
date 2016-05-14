Ogame Bot
=============

This is bot for browser game ogame. 
Queue with tasks for bot can be filled in web gui or by modifying the queue.json file.

TODO
-----
- commands for more planets (now is here only one hardcoded planet)
- refactor commands to have UUID and planet coordinates. Create manager for commands adding, sorting and deleting. Use QueueRepository only by manager.
- maybe export/import command lists and routines
- think about routines implementation (repetitious commands)
- repetitious fleet checking for attacks
- commands for sending fleet
- automatically buy new probes when probes are destroyed when gathering data and buy new sattelites when destroyed (Maintain quantity in Ogame Automizer)
- logging to monolog
	- log attacks, full storages and other important messages to some android app
- repetitious tasks
	- set repeating frequency
	- checking if someone attacks me
	- galaxy scanning
	- probing scanned, inactive players
	- farming scanned players 
		- save planet status (from Ogame Automizer - Attack Status)
		- set how many minimal resources to gather
		- set from which planet to scan
		- set espionage probe count to send
			- "OGame Options > General > Spy Probes" in ogame menu
		- set maximum deuterium consumption
		- set expected resourced ratio
		- set scanning range constraints
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
- sorting and deleting commands in GUI
- maybe try to integrate Ogame Automizer constuctor for mines on planet optimization
- maybe try to implement generating construction list from Ogame Automizer
- randomize intervals, set how slow or big should be waiting betewwn actions (slider more bot - more human)
- setting automating resources sending and automatic building (e.g. for satellites for Graviton technology)