#!/usr/local/bin/python3.5

import sys
import os

import psycopg2
import numpy as np
import pandas as pd
import pandas.io.sql as psql
import datetime

import argparse

import re
import csv

import json

parser = argparse.ArgumentParser()

infile = None
user_id = None
output_dir = None

parser.add_argument('-i', action='store', dest='infile',
                    help='csv file as input. (i.e. filename.csv)')
parser.add_argument('-u', action='store', dest='user_id',
                    help='the postgres database user_id attribute of the users table of the login database as input. (i.e. fba9372fe2)')
parser.add_argument('-o', action='store', dest='output_dir',
                    help='output directory as input. (i.e. $HOME)')

parser.add_argument('--version', action='version', version='%(prog)s 1.0')

results = parser.parse_args()

infile = results.infile
user_id = results.user_id
output_dir = results.output_dir

if(infile == None):
    print('\n')
    print('error: please use the -i option to specify the csv file as input')
    print('infile =' + ' ' + str(infile))
    print('\n')
    parser.print_help()
    sys.exit(1)
if(user_id == None):
    print('\n')
    print('error: please use the -u option to specify the postgres database user_id attribute of the users table of the login database as input')
    print('user_id =' + ' ' + str(user_id))
    print('\n')
    parser.print_help()
    sys.exit(1)
if(output_dir == None):
    print('\n')
    print('error: please use the -o option to specify the output directory as input')
    print('output_dir =' + ' ' + str(output_dir))
    print('\n')
    parser.print_help()
    sys.exit(1)


if not os.path.exists(output_dir):
    os.makedirs(output_dir)

python_path = sys.argv[0]
app_dir = os.path.dirname(os.path.realpath(python_path))


timeinseconds = datetime.datetime.now().strftime("%s")

#sys.exit()
clubroot_data_dir = os.path.join(os.path.dirname(app_dir), "clubroot")

geojson_tracker_file = os.path.join(clubroot_data_dir, "tracker_geojson.js")
#print(geojson_tracker_file)
#basename = os.path.basename(geojson_tracker_file)

#(filename, ext) = os.path.splitext(basename)
#print(filename, ext)

geojson_tracker_file_temp = os.path.join(clubroot_data_dir, "_".join(["tracker_geojson", "timeinseconds.js"]))
#print(geojson_tracker_file_temp)

# Get data from previously existing geojson input file.
prev_geojson_data = {}
prev_geojson_infile = os.path.join(os.path.dirname(app_dir), "clubroot/tracker_geojson.js")
prev_geojson_string = "";
with open(prev_geojson_infile) as prev_geojson_file:
	for line in prev_geojson_file.readlines():
		#print(line.replace("var clubroot_tracker_coords = ", ""))
		prev_geojson_string += line.replace("var clubroot_tracker_coords = ", "")

#print(prev_geojson_string)
prev_geojson_data = json.loads(prev_geojson_string)
#print(prev_geojson_data)

# Get count of the amount of entries in the previously existing geojson input file.
prev_line_count = len(prev_geojson_data["features"])

#print(prev_line_count)
#sys.exit()
	
PGHOST = "localhost"
#PGDATABASE = "clubroot_tracker_login"
PGDATABASE = "login"

PGUSER = "postgres"
PGPASSWORD = "password"

## ****** LOAD PSQL DATABASE ***** ##
try:

	# Set up a connection to the postgres server.
	conn_string = "host=" + PGHOST + " port=" + "5432" + " dbname=" + PGDATABASE + " user=" + PGUSER + " password=" + PGPASSWORD
	conn = psycopg2.connect(conn_string)
	print("Connected!")

	# Create a cursor object
	cursor = conn.cursor()

	# Print PostgreSQL Connection properties
	print ( conn.get_dsn_parameters(),"\n")

	# Print PostgreSQL version
	cursor.execute("SELECT version();")
	record = cursor.fetchone()
	print("You are connected to - ", record,"\n")

	sql_command = "SELECT * FROM {} WHERE user_id='{}';".format("users", user_id)
	#print (sql_command)

	# Load the data
	data = pd.read_sql(sql_command, conn)

	#print(data)
	#email firstname  lastname academictitle            institution country

	email = data['email'][0]
	firstname = data['firstname'][0]
	lastname = data['lastname'][0]
	academictitle = data['academictitle'][0]
	institution = data['institution'][0]
	country = data['country'][0]

	#sys.exit()

	features = []
	with open(infile) as csv_file:
		csv_reader = csv.reader(csv_file, delimiter=',')
		line_count = 0
		id_count = prev_line_count
		for row in csv_reader:
			if line_count != 0:
				province = row[0]
				location = row[1]
				lat = float(row[2])
				long = float(row[3])

				coordinates = []
				coordinates.append(lat)
				coordinates.append(long)

				pathogen_common_name = row[4]
				display = row[5]
				print('province = ' + str(province))
				print('location = ' + str(location))
				print('lat = ' + str(lat))
				print('long = ' + str(long))
				print('display = ' + str(display))

				coordinates_data = {}
				point_coord = {}
				properties = {}
				point_coord["type"] = "Point"
				point_coord["coordinates"] = coordinates


				coordinates_data["geometry"] = point_coord


				properties["popupContent"] = display

				properties["pathogenCommonName"] = pathogen_common_name
				properties["province"] = province
				properties["location"] = location

				properties["email"] = email
				properties["firstname"] = firstname
				properties["lastname"] = lastname
				properties["academictitle"] = academictitle
				properties["institution"] = institution
				properties["country"] = country

				coordinates_data["type"] = "Feature"
				coordinates_data["properties"] = properties
				coordinates_data["id"] = id_count
				features.append(coordinates_data)
			line_count += 1
			id_count += 1

	new_geojson_data = {}
	new_geojson_data["type"] = "FeatureCollection"
	
	for feature in features:
		prev_geojson_data["features"].append(feature)
	
	new_geojson_data["features"] = prev_geojson_data["features"]
	#print(new_geojson_data)
	
	
	geojson_outfile = os.path.join(output_dir, 'tracker_geojson.js')
	geojson_file = open(geojson_outfile, "w+")
	geojson_file.write("var clubroot_tracker_coords" + " " + "=" + " " + json.dumps(new_geojson_data, indent=4))
	geojson_file.close()

	
	os.system('cp {source_file} {dest_file} && rm {source_file}'.format(source_file=geojson_tracker_file, dest_file=geojson_tracker_file_temp))  
	os.system('cp {source_file} {dest_file}'.format(source_file=geojson_outfile, dest_file=geojson_tracker_file))  
	
except (Exception, psycopg2.Error) as error :
    print ("Error while connecting to PostgreSQL", error)
finally:
    #closing database connection.
        if(conn):
            cursor.close()
            conn.close()
            print("PostgreSQL connection is closed")


