Vote for Points - A FluxCP Addon
=====================

Incentivize your players to vote for your server through a voting system directly integrated to your FluxCP.

# Features
1. **Vote Time Interval** - Only allows players to vote every 12 hours for a voting site (Default: 12 hours).

2. **Time Left** - Provides a count down until the voting site can be voted on.

3. **Vote Points** - A selection of either [Credits, Vote Points, Cash Points] as reward for voting. (Default: 1)

4. **Add, Delete or Edit** a voting site.

5. **List** all voting sites.

6. **Voting Site Image** - Able to upload the image for the voting site or use the Image URL instead. Images are required for your voting site.

7. **Vote Name** - A label to avoid the confusion of the voting site.

8. **Proxy Check** - Able to detect if the user is using proxy.

9. **IP Check** - Able to check if the user has already voted by its ip address.

10. **Vote Logging (New)** - Log whenever a player votes. Admin is allowed to delete an entry in the vote log.

11. **Accompanying NPC script** - Able to buy items from an NPC Shop in game or to redeem a reward.

## Compability
Tested on Xantara's FluxCP for rAthena - https://github.com/m...ntara/fluxcp-rA

## Rules
Do not steal the credit of this work.

## Requirements
- PHP 5.2+
- MySql

# Installation

1. Create a folder named `voteforpoints` in your `flux_root_folder/addons` folder.

2. Download this addon and extract all the files into the `voteforpoints` folder.

3. Import the sql files from `schemas/logindb` folder.

4. Create a folder in `flux_root_folder/themes/default/img/votes/` or replace `votes` with whichever name you use in the configuration.

5. Give permissions to the `flux_root_folder/themes/default/img/votes/` folder so that your flux website can upload images. You can do this by using the terminal, going to the `flux_root_folder/themes/default/img/` directory and typing `chmod 777 votes`.

6. Copy the file `voteforpoints.txt` from npc folder and paste it to your `rAthena_root_folder/npc/custom/`

7. Edit the file `scripts_custom.conf` and add the line `npc: npc/custom/voteforpoints.txt`

8. Change the settings in `voteforpoints` to your liking.

9. Done

## Bug Reports

If you find a bug, please contact me.

Github: http://github.com/Feefty  
Email: kingfeefty@gmail.com  
rAthena: Feefty

Feel free to buy me a coffee  
Paypal: keinstah@gmail.com
