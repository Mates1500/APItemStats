# APItemStats
League of Legends AP Item stats between patch 5.11 and 5.14, includes all sorts of stuff.

The main goal of this project was just to make something nice looking for the [Riot API Challenge 2.0](https://developer.riotgames.com/discussion/announcements/show/2lxEyIcE)

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
Go ahead and ask on the issue tracker, currently I'm a little tired from working on this project for 4 days straight, so the project description might not be complete yet.

##Third party tools used in this project
- [Riot Games API](https://developer.riotgames.com/) (you don't say?!)
- [jQuery](https://jquery.com/) (so working with button events and changing text isn't nearly as painful as vanilla Javascript)
- [jQuery.cookie](https://github.com/carhartl/jquery-cookie) (so we can store user preference data without adding custom spaghetti code)
- [bootstrap](http://getbootstrap.com/) (making a decent looking website from a complete scratch is heroic mode nowadays)
- [bootstrap Yeti theme](https://bootswatch.com/yeti/) (the default one is just not my style)
- [Google Charts](https://developers.google.com/chart/?hl=en) (for the main page table)
- [Chart.js](http://www.chartjs.org/) (these graphs are so beatiful)



