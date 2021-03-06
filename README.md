# APItemStats
League of Legends AP Item stats between patch 5.11 and 5.14, includes all sorts of stuff.

The main goal of this project was just to make something nice looking for the [Riot API Challenge 2.0](https://developer.riotgames.com/discussion/announcements/show/2lxEyIcE)

The entire project was made just in **4 days**, because I found out about the challenge quite early and thought to myself - naah, I'll do it later. Turns out it was almost the end of August and I was going on vacation to Italy for over a week, so it was quite intense to make the entire thing work in the way it does. It could definitely use way more polish, that's for sure.

The user can switch between all the regions, including an "all" switch, that will output average data of all regions, based on the number of matches in the database of each region.

On the main page, on the first visit, the user is presented with the item results of NA matches, however, the user can switch freely between all the regions, on region switch, AJAX requests the results from another PHP page, therefore **no refresh is required**.

Region preference (last region clicked by the user) is stored in cookies and retrieved on pageload (if the cookie exists)

Once the user selects an item, the user is presented with 4 kinds of different graphs.

The **statistics tracked** by this app are
- Both for **5.11 and 5.14**
  - **Item Description** (so an item change between the patches is apparent) 
  - **Win rate** (number of matches won are stored in the database, divided by the number of valid matches from that region for % based result)
  - **Pick rate** (same as above)
  - **Average purchase time** (stored in the same ms format as Riot does, converted to mm:ss by javascript)
  - **Median purchase time** (same as above, for this to be relevant, ALL the timestamps are stored in the DB, but the user requests cached data to reduce server load)

###Just visiting, show me the thing!
Alright, demo's [available here](http://mates1500.com/apitemstats/)
  
![alt tag](https://raw.githubusercontent.com/Mates1500/APItemStats/master/images/splash_small.png)  



###How to run it yourself
- Make an empty MySQL database, preferably called apitems (default in connect.php)
- Import **dbstructure.sql**, that's inside the **INSTALL folder** into your MySQL database
- In **connect.php** set your **MySQL login, password, db name**

``` $mysqli = new mysqli('localhost', 'MYSQL_LOGIN', 'MYSQL_PW', 'DB_NAME'); ```

- In **apikey.php** set your **Riot API key** (if you don't have one, visit https://developer.riotgames.com/)

```$apikey = "YOUR_API_KEY_HERE";```

- In **password.php** set your admin **password** that's already **encoded in md5**

``` $password = "YOUR_MD5_PASSWORD_HERE"; ```

- Run **fillitemstats.php** at least once
- I recommend setting these variables in **php.ini** as follows, to see the fetching progress as it goes

```
implicit_flush = yes 
output_buffering = off
```

- Go to **updatestatsform.php** to start fetching the matches into the database
- Once you're done fetching, go and do some caching instead! Run **createandfillcacheddata.php** every time you wanna see the fetched results reflected on the main page.
- **index.php** should be **ready to roll** now

##Questions?

The **code is BAD**, I know that, it was a rushed project a few days before the end of the API challenge, and I probably won't be returning to this in the future, check out my portfolio for my other projects with way better OOP structure. This is just a procedurally programmed one-use mess.

Go ahead and ask on the issue tracker, currently I'm a little tired from working on this project for 4 days straight, so the project description might not be complete yet.

Most of the code should be self-explanatory, hopefully. It's mostly just SQL queries over and over again, fetching, manipulating and adding data.

##Known issues I might fix later
- Code is spaghetti in places (especially updatestats.php)
- Pick rate seems dodgy (quite low), most likely because of the reason above
- The folder structure sucks, too many files in root folder, file names are way too long sometimes
- Seeing progress especially in updatestats.php without implicit_flush on is quite hard, I would have to restructure the entire file to make it work in a more feasible way
- Changing region on item detail doesn't change the parameter in the address bar

##Third party tools used in this project
- [Riot Games API](https://developer.riotgames.com/) (you don't say?!)
- [jQuery](https://jquery.com/) (so working with button events and changing text isn't nearly as painful as vanilla Javascript)
- [jQuery.cookie](https://github.com/carhartl/jquery-cookie) (so we can store user preference data without adding custom spaghetti code)
- [bootstrap](http://getbootstrap.com/) (making a decent looking website from a complete scratch is heroic mode nowadays)
- [bootstrap Yeti theme](https://bootswatch.com/yeti/) (the default one is just not my style)
- [Google Charts](https://developers.google.com/chart/?hl=en) (for the main page table)
- [Chart.js](http://www.chartjs.org/) (these graphs are so beatiful)



