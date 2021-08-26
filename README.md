# twitchbot-endpoints
endpoints for twitchbots

The plugin will basically scrapped data from leekduck.com and create the message to send back to twitch. So no need to update the command it'll pull updated data as soon as leekduck.com updates it. it should work as long as there aren't any mayor changes to leekduck.com url or html structure.

# Usage

This is a wordpress plugin you could install in your own wordpress site and then call on your prefered twitchbot custom command.

I have it set up on my site and nightbot like this you could call it from my site if preferred not sure how the scalability will be but i guess we can worry about if people actually decide to use it.

## Example

the endpoint is `https://pnx.world/wp-json/twitchbots/v1/current-raid-boss/?tier=tier1` replace the domain if you are self-hosting it. 

### Nightbot Command

You can add a command like `!raidbosses` and then set the message to.
```
$(urlfetch https://pnx.world/wp-json/twitchbots/v1/current-raid-boss/?tier=$(1))
```

Command usage: `!raidbosses tier1` or `!raidbosses tier3` or `!raidbosses tier5` or `!raidbosses mega`. the endpoint will default to tier1 if the wrong argument is passed.

### Screenshots

![Alt text](/screenshots/raidbosses-command-example.png?raw=true "Raid Bosses Command Twitch Example")

Im thiking about other endpoints to add here that will be useful for my streams and possibly others.

We are new streamers if you like pokemon, animal crossing, zelda, music, the office, spongebob, 90s stuff in general or just to have some fun please think about stopping by we will appreaciate it [Twitch.tv/pammyxavioh](https://twitch.tv/pammyxavioh). Also if you use this or my public endpoint i would appreaciate a shoutout on social media @pammyxavioh on insta,twitter,tiktok,twitch and youtube.
