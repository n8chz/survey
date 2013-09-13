#!/usr/bin/python

# Create a graphic with ideology names plotted according to x,y coordinates
# for each ideology
# corresponding to a reduction to two dimensions
# using principal component analysis

# outline of this routine cribbed from http://planet.mysql.com/entry/?id=29470
# version.py -- Fetch and display the MySQL database server version.

print "\"mkidmap.py\" started\n"

# import the MySQLdb and sys modules
import MySQLdb
import sys
import mdp # see http://mdp-toolkit.sourceforge.net/
print "\"mdp\" opened\n"
import array
from scipy import *
import Image
import ImageDraw
import random
import math
# import os

width=512
height=512


# open a database connection
# be sure to change the host IP address, username, password and database name to match your own
connection = MySQLdb.connect (host = "localhost" , user = "scott", passwd = "tiger", db = "ais")


# prepare a cursor object using cursor() method
cursor = connection.cursor ()


# execute the SQL query using execute() method.
cursor.execute ("select ideology, question, answer from answer order by ideology, question") #create xy plot of ideologies


stats=[]
ideologies=[]
prev=0

# fetch a single row using fetchone() method.
row = cursor.fetchone ()
while row:
    ideology = row[0]
    if ideology != prev:
         stats.append([])
         cursor2 = connection.cursor()
         cursor2.execute("select ideology from ideology where id=%s" % ideology)
         idrow=cursor2.fetchone()
         idname=idrow[0]
         if idname=="":
             idname="[ideology #%d]" % ideology
         ideologies.append(idname)
    stats[-1].append(float(row[2]))
    prev=ideology
    row=cursor.fetchone()


raw=array(stats)
cooked=mdp.pca(raw, output_dim=2) # see http://nullege.com/codes/search/mdp.pca


(xmax, ymax) = cooked.max(0) # max value in each column vector of y, see http://mathesaurus.sourceforge.net/numeric-numpy.html
(xmin, ymin) = cooked.min(0) # And min.  These will be used to interpolate the x,y coordinates for plotting


idmap=Image.new("RGB", (width+240, height+12), (128,128,128))
draw=ImageDraw.Draw(idmap)
for i in range(len(ideologies)):
    ts=draw.textsize(ideologies[i]) # center the name over its coordinates
    x=width*(cooked[i,0]-xmin)/(xmax-xmin)-math.trunc(ts[0]/2)+120
    y=height*(cooked[i,1]-ymin)/(ymax-ymin)-math.trunc(ts[1]/2)+6
    newcolor=[]
    for j in range(3): # generate a random color, one that contrasts w. midtone gray background
        newrand=random.random()+random.random()
        newrand = (newrand-2 if newrand>1 else newrand)
        newrand=math.trunc(128*newrand+128)
        newcolor.append(newrand)
    newcolor=tuple(newcolor)
    draw.text((x, y),ideologies[i], fill=newcolor)

# os.remove("idmap.png")
idmap.save("idmap.png", format="PNG")


# close the cursor object
cursor2.close()
cursor.close ()

# close the connection
connection.close ()



# render the xhtml page

print "Content-Type: text/html"
print

print """\
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>

<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=../ais\" />
</head><body></body></html>

"""

# exit the program
sys.exit()

