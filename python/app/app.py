«#!/usr/bin/env python
# -*- coding: utf-8 -*-

from flask import Flask, render_template, g, request, redirect, url_for,  make_response
import logging, psycopg2


app = Flask(__name__)




@app.route("/")
def home():
    return render_template("index.html");



@app.route("/part1.html", methods=['GET'])
def login():


    return render_template("part1.html");


@app.route("/part1_vulnerable", methods=['GET', 'POST'])
def part1_vulnerable():
    logger.info("---- part1_vulnerable ----")

    if request.method == 'GET':
        password = request.args.get('v_password') 
        username = request.args.get('v_username') 
        remember = request.args.get('v_remember') 
    else:
        password = request.form['v_password']
        username = request.form['v_username']
        remember = request.form['v_remember']
        
    

    logger.info("v_password  -> " + password);
    logger.info("v_username  -> " + username);
    logger.info("v_remember  -> " + remember);


    return "/part1_vulnerable "


@app.route("/part1_correct", methods=['GET', 'POST'])
def part1_correct():
    


    return "/part1_correct"



@app.route("/part2.html", methods=['GET'])
def part2():



    return render_template("part2.html");


@app.route("/part2_vulnerable", methods=['GET', 'POST'])
def part2_vulnerable():
    
   

    return "/part2_vulnerable"


@app.route("/part2_correct", methods=['GET', 'POST'])
def part2_correct():
    

    return "/part2_correct"






@app.route("/part3.html", methods=['GET'])
def part3():


    return render_template("part3.html");


@app.route("/part3_vulnerable", methods=['GET', 'POST'])
def part3_vulnerable():
    
   

    return "/part3_vulnerable"


@app.route("/part3_correct", methods=['GET', 'POST'])
def part3_correct():
    

    return "/part3_correct"


@app.route("/demo", methods=['GET', 'POST'])
def demo():
    logger.info("\n DEMO \n");   

    conn = get_db()
    cur = conn.cursor()

    logger.info("---- users  ----")
    cur.execute("SELECT * FROM users")
    rows = cur.fetchall()

    for row in rows:
        logger.info(row)

    for row in rows:
        logger.info(row)

    logger.info("---- messages ----")
    cur.execute("SELECT * FROM messages")
    rows = cur.fetchall()
 
    for row in rows:
        logger.info(row)

    logger.info("---- books ----")
    cur.execute("SELECT * FROM books")
    rows = cur.fetchall()
 
    for row in rows:
        logger.info(row)

    conn.close ()
    logger.info("\n---------------------\n\n") 

    return "/demo"


##########################################################
## DATABASE ACCESS
##########################################################

def get_db():
    db = psycopg2.connect(user = "ddss-database-assignment-2",
                password = "ddss-database-assignment-2",
                host = "db",
                port = "5432",
                database = "ddss-database-assignment-2")
    return db





##########################################################
## MAIN
##########################################################
if __name__ == "__main__":
    logging.basicConfig(filename="log_file.log")

    logger = logging.getLogger('logger')
    logger.setLevel(logging.DEBUG)
    ch = logging.StreamHandler()
    ch.setLevel(logging.DEBUG)

    # create formatter
    formatter = logging.Formatter('%(asctime)s [%(levelname)s] %(name)s:  %(message)s')

    # add formatter to ch
    ch.setFormatter(formatter)

    # add ch to logger
    logger.addHandler(ch)

    logger.info("\n---------------------\n\n")

    app.run(host="0.0.0.0", debug=True, threaded=True)





