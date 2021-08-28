# twitchbot-endpoints
endpoints for twitchbots

The plugin will basically scrapped data from leekduck.com and create the message to send back to twitch. So no need to update the command it'll pull updated data as soon as leekduck.com updates it. it should work as long as there aren't any mayor changes to leekduck.com url or html structure.

The pogonews endpoint scrappes the content from pokemongolive

the pokenews endpoint was much easier to do as pokemon.com is already using a public api endpoint that outputs a JSON object.

# Usage

This is a wordpress plugin you could install in your own wordpress site and then call on your prefered twitchbot custom command.

I have it set up on my site and nightbot like this you could call it from my site if preferred not sure how the scalability will be but i guess we can worry about if people actually decide to use it.

## Example

### Raidbosses command endpoint

the endpoint is `https://pnx.world/wp-json/twitchbots/v1/current-raid-boss/?tier=tier1` replace the domain if you are self-hosting it. 

### Nightbot raidbosses Command

You can add a command like `!raidbosses` and then set the message to.
```
$(urlfetch https://pnx.world/wp-json/twitchbots/v1/current-raid-boss/?tier=$(eval if("$(1)" == 'null' ){ "tier1"; }else{ "$(1)"; }))
```

Command usage: `!raidbosses tier1` or `!raidbosses tier3` or `!raidbosses tier5` or `!raidbosses mega`. the endpoint will default to tier1 if the wrong argument is passed.

### Screenshots

![Raid Bosses Command Twitch Example](/screenshots/raidbosses-command-example.png?raw=true "Raid Bosses Command Twitch Example")

### Pogonews command endpoint

the endpoint is `https://pnx.world/wp-json/twitchbots/v1/current-raid-boss/?tier=tier1` replace the domain if you are self-hosting it. 

### Nightbot Pogonews Command

You can add a command like `!pogonews` and then set the message to.
```
$(urlfetch https://pnx.world/wp-json/twitchbots/v1/latest-pogo-news/?top=$(eval if("$(1)" == 'null' ){ "1"; }else{ "$(1)"; }))
```

Command usage: `!pogonews 1` or `!pogonews top1` or `!pogonews 2` or `!pogonews top2`. the endpoint will default to top 1 if the wrong/empty argument is passed. It limits at 2 do to character limit, there is the possibility of maybe using a url shortener to allow for more but not sure there is much usage in having really more than the top1.

### Screenshots

![Pogonews Command Twitch Example](/screenshots/pogonews-command-example.png?raw=true "Pogonews Command Twitch Example")

### Nightbot Pokonews Command

You can add a command like `!pokonews` and then set the message to.
```
$(urlfetch https://pnx.world/wp-json/twitchbots/v1/latest-poke-news/?cat=$(eval if("$(1)" == 'null' ){ "vg"; }else{ "$(1)"; })&top=$(eval if("$(2)" == 'null' ){ "1"; }else{ "$(2)"; }))
```

Command usage: `!pokonews` or `!pogonews tcg` or `!pogonews tv` or `!pogonews vg top2`. the endpoint will default to top 1 if the wrong/empty argument is passed. It limits at 2 do to character limit, there is the possibility of maybe using a url shortener to allow for more but not sure there is much usage in having really more than the top1. It also defaults to returning the video game news.

### Screenshots

![Pokenews Command Twitch Example](/screenshots/pokenews-command-example.png?raw=true "Pokenews Command Twitch Example")

Im thiking about other endpoints to add here that will be useful for my streams and possibly others.

We are new streamers if you like pokemon, animal crossing, zelda, music, the office, spongebob, 90s stuff in general or just to have some fun please think about stopping by we will appreaciate it [Twitch.tv/pammyxavioh](https://twitch.tv/pammyxavioh). Also if you use this or my public endpoint i would appreaciate a shoutout on social media @pammyxavioh on insta,twitter,tiktok,twitch and youtube.
