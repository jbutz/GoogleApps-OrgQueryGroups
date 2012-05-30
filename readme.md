Organization based Query Groups for Google Apps
===============================================

This utility uses the <a href="https://code.google.com/p/google-apps-manager/">Google Apps Manager</a> and allows you to syncronize everyone from an organization, containing a certain string, to a group.

**Usage:**

<code>./googleQuery.php [--detail] [--noadd] [--noremove] --group GROUP_NAME --org SEARCH_STRING</code>

**Example:**

This example will pull all of the members that have an organization with the string "Location 1" add syncronize that with the group *loc1*.

<code>./googleQuery.php --group loc1 --org "Location 1"</code>


How the Search Works
--------------------
The script goes out and gets a listing of your organization's hierarchy and then looks for the given string in the names. After that it gets all of the members of those organizations and figures out the difference from the group and then syncronizes them. To better explain what it is doing take a look at the example below:

<pre>
My Company (root)
 |- Admins
 |- domain1.com
 |	|- Location 1
 |	|- Location 4
 |
 |- domain2.com
    |- Location 2
  	|- Location 3
	|- Location 4
</pre>

The organization hierarchy you see above is an example where there is one company that has, for our purposes, a subsidiary that uses a different domain name. They decided to split these domains out in their Organizations in Google Apps. Lets say they placed their users based on their email's primary domain and where they work.

When the script is searching what it sees is shown below:

<pre>
/Admins
/domain1.com
/domain1.com/Location 1
/domain1.com/Location 4
/domain2.com
/domain2.com/Location 2
/domain2.com/Location 3
/domain2.com/Location 4
</pre>

So if we search for "Location 4" we will get users from domain1.com and domain2.com. You could also search "domain1.com" and get everyone under the domain1.com tree.

Setting up the script
--------------------

In order to get this script going the most difficult part is getting <a href="https://code.google.com/p/google-apps-manager/">Google Apps Manager</a> up and running, which isn't that hard. They have instructions on their website for you to follow. If you want to set this script up on a server you may not be able to open a browser window. If that is the case set up GAM on your computer and then copy the oath.txt file to your gam directory on the server. Then adjust the path to the GAM executable in the script. It is marked with comments.