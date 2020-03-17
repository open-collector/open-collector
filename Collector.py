import eel
<<<<<<< HEAD
import json
import os
import platform

'''
#packages needed to shut down and restart localhost if need be
from psutil import process_iter
from signal import SIGKILL
'''

@eel.expose
def ask_python_exp(exp_name):
    experiment_file = open("web/User/Experiments/" + exp_name + ".json", "r")
    experiment_file  = experiment_file.read()
    experiment_json = json.loads(experiment_file)
    eel.python_gives_exp(experiment_json)

@eel.expose
def delete_exp(exp_name):
    os.remove("web/User/Experiments/" + exp_name + ".json")# delete file

@eel.expose
def load_master_json():
    #check if the uber mega file exists yet
    try:
        master_json = open("web/User/master.json", "r")
    except:
        master_json = open("web/kitten/Default/master.json", "r")
    finally:
        master_json = master_json.read()
        master_json = json.loads(master_json)
        eel.load_master_json(master_json)


@eel.expose
def pull_open_collector(username,
                        password,
                        organisation,
                        repository):
    if(organisation == ""):
        organisation = username
    try:
        push_collector(username,
                       password,
                       organisation,
                       repository,
                       "backup before updating from open-collector repository")
        os.system("git remote set-url --push origin https://github.com/" + organisation +"/" + repository + ".git")
        pull_open_collector_only()

=======
import os
import json
import tkinter as tk

from tkinter import filedialog
from tkinter import *
from tkinter.filedialog import askopenfile
from tkinter.messagebox import showerror

import base64
import pandas as pd
import numbers
import numpy as np

@eel.expose
def create_space(repository_name,
                 github_organisation,
                 github_username,
                 github_password):
    print(repository_name)
    print(github_username)
    if github_organisation != "":
        repository_dir = github_organisation + "/" + repository_name
    else:
        repository_dir = repository_name

    orig_dir = os.getcwd();
    #os.mkdir("web/" + repository_name)
    os.chdir(orig_dir + "/web")

    #root = tk.Tk()
    #root.withdraw()
    #file_path = filedialog.askdirectory(title="Where shall we install Collector?")
    #print(file_path);
    #os.chdir(file_path)
    os.system("git clone https://github.com/open-collector/open-collector " + repository_name)


    print("You now have a working version of Collector!")
    print("repository_name")
    print(repository_name)
    os.chdir(repository_name)
    os.system("hub create " + repository_dir)
    print("git push https://" + github_username + ":" + github_password + "@github.com/" + repository_dir + ".git")
    os.system("git push https://" + github_username + ":" + github_password + "@github.com/" + repository_dir + ".git")
    eel.python_message("Succesfully updated online version of collector!")

    os.chdir(orig_dir)
    settings = open("settings.json", "r+")
    read_settings = json.loads(settings.read())
    read_settings[repository_name] = {
        "online": "true",
        "organisation": github_organisation,
        "repository": repository_name,
        "username": github_username
    }
    settings.close()

    settings = open("settings.json", "r+")
    settings.write(json.dumps(read_settings))
    settings.close()

files_folder = os.listdir()
@eel.expose
def startup():
    if "settings.json" in files_folder:
        settings = open("settings.json","r")
        eel.load_settings(settings.read())

@eel.expose
def pull_open_collector(username,
                       organisation,
                       repository):
    if(organisation == ""):
        organisation = username
    try:
        os.system("git remote set-url --push origin https://github.com/" + organisation +"/" + repository + ".git")
        os.system("remote set-url origin https://github.com/open-collector/open-collector.git")
        os.system("git fetch origin master")
        os.system("git merge -X theirs origin/master --allow-unrelated-histories -m'update from open-collector'")
>>>>>>> 94b84c7904574e43669bbc18777b7c6e9dc17b0f
    except:
        print("Something went wrong")
    finally:
        print("Attempt to update finished")
        #should trigger restart here

<<<<<<< HEAD
def pull_open_collector_only():
    os.system("remote set-url origin https://github.com/open-collector/open-collector.git")
    os.system("git fetch origin master")
    os.system("git merge -X theirs origin/master --allow-unrelated-histories -m'update from open-collector'")
    if platform.system().lower() == "windows":
        os.system("WindowsCompileCollector.bat")
    #currently mac and linux users have to use python versions.
=======
>>>>>>> 94b84c7904574e43669bbc18777b7c6e9dc17b0f

@eel.expose
def push_collector(username,
                   password,
                   organisation,
<<<<<<< HEAD
                   repository,
                   this_message):
=======
                   repository):
>>>>>>> 94b84c7904574e43669bbc18777b7c6e9dc17b0f
    print("trying to push to the repository")
    if organisation == "":
        organisation = username
    #create repository if that fails
    #os.system("git push https://github.com/open-collector/open-collector")
    try:
<<<<<<< HEAD
        print(this_message)
        os.system("git add .")
        os.system('git commit -m "' + str(this_message) + '"')
=======
        os.system("git add .")
        os.system("git commit -m 'pushing from local'")
>>>>>>> 94b84c7904574e43669bbc18777b7c6e9dc17b0f
        os.system("git push https://" + username + ":" + password + "@github.com/" + organisation + "/" + repository+ ".git")
    except:
        print("looks like I need to create a repository to push to")

        #need to make this a repository
        if(organisation != username):
            create_repository = organisation + "/" + repository
        else :
            create_repository = repository
        os.system('git init')
        os.system('eval "$(ssh-agent -s)"')
        os.system("hub create " + create_repository)
        os.system("git add .")
        os.system("git commit -m 'pushing from local'")
        os.system("git push https://" + username + ":" + password + "@github.com/" + organisation + "/" + repository+ ".git")
        #git config receive.denyCurrentBranch refuse
        #git push --set-upstream py2rm-collector

        #os.system("git push https://" + username + ":" + password + "@github.com/" + organisation + "/" + repository)
    finally:
        print("It all seems to have worked - mostly speaking")

<<<<<<< HEAD



@eel.expose
def rename_experiment(old_name,
                      new_name):
    #rename the experiment json
    #rename the experiment folder
    print("not yet implemented")

@eel.expose
def rename_survey(old_name,
                  new_name):
    try:
        os.rename("web/User/Surveys/" + old_name,
                  "web/User/Surveys/" + new_name)
        eel.update_master_surveys(old_name,
                                  new_name)
    except Exception as err:
        print(err)
        eel.python_bootbox(str(err))



@eel.expose
def request_sheet(experiment,
                  sheet_type,
                  sheet_name):

    if os.path.isfile("web/User/Experiments/" + experiment + "/" + sheet_name):
        sheet_content = open("web/User/Experiments/" + experiment + "/" + sheet_name, "r")
        sheet_content = sheet_content.read()
    else:
        sheet_content = experiment

    eel.receive_sheet(sheet_content,
                      sheet_type,
                      sheet_name)

@eel.expose
def save_data(experiment_name,
              participant_code,
              completion_code,
              responses):
=======
@eel.expose
def update_collector(location,
                     this_rep_info,
                     password):
    this_rep_info = json.loads(this_rep_info)
    organisation  = this_rep_info["organisation"]
    repository    = this_rep_info["repository"]
    username      = this_rep_info["username"]

    if organisation == "":
        organisation = username

    #repository = split the location to get the repository name
    os.chdir(location)
    os.system("git pull https://github.com/open-collector/open-collector")

    eel.python_message("Succesfully updated on local machine!")

    if this_rep_info["online"]:
        os.system("git add .")
        os.system("git commit -m 'update from master'")
        os.system("git push https://" + username + ":" + password + "@github.com/" + organisation + "/" + repository)
        eel.python_message("Succesfully updated <b>" + organisation + "/" + repository)
        #git push https://username:password@myrepository.biz/file.git --all




@eel.expose
def load_master_json():
    print("hi")
    #check if the uber mega file exists yet
    try:
        master_json = open("web/Local/master.json", "r")
    except:
        master_json = open("web/kitten/Default/master.json", "r")
    finally:
        master_json = master_json.read()
        master_json = json.loads(master_json)
        eel.load_master_json(master_json)

@eel.expose
def save_data(experiment_name,participant_code,responses):
>>>>>>> 94b84c7904574e43669bbc18777b7c6e9dc17b0f
    print("experiment_name")
    print(experiment_name)
    print("participant_code")
    print(participant_code)
    print("responses")
    print(responses)
<<<<<<< HEAD
    if os.path.isdir("web/User/Data") == False:
        os.mkdir("web/User/Data")
    if os.path.isdir("web/User/Data/" + experiment_name) == False:
        os.mkdir("web/User/Data/" + experiment_name)
    experiment_file = open("web/User/Data/" + experiment_name+ "/" + participant_code + "-" + completion_code + ".csv", "w", newline='')
=======
    if os.path.isdir("web/Local/Data") == False:
        os.mkdir("web/Local/Data")
    if os.path.isdir("web/Local/Data/" + experiment_name) == False:
        os.mkdir("web/Local/Data/" + experiment_name)
    experiment_file = open("web/Local/Data/" + experiment_name+ "/" + participant_code + ".csv", "w")
>>>>>>> 94b84c7904574e43669bbc18777b7c6e9dc17b0f
    experiment_file.write(responses)


@eel.expose
def save_experiment(experiment_name,experiment_json):
<<<<<<< HEAD
    errors = ""
    print("trying to save experiment")
    if os.path.isdir("web/User/Experiments") == False:
        os.mkdir("web/User/Experiments")
    print(experiment_name)
    print(json.dumps(experiment_json))
    experiment_file = open("web/User/Experiments/" + experiment_name + ".json", "w")
    experiment_file.write(json.dumps(experiment_json))

    python_message = "Experiment saved"
    eel.python_bootbox(python_message)

    if os.path.isdir("web/User/Experiments/" + experiment_name) == False:
        os.mkdir("web/User/Experiments/" + experiment_name)

    python_message = python_message + "...<br> saving <b>conditions.csv</b>"
    eel.python_bootbox(python_message)
    try:
        this_cond_file = open("web/User/Experiments/" + experiment_name + "/conditions.csv", "w", newline='')
        this_cond_file.write(experiment_json["python_conditions"])
    except:
        errors += "...<br><span class='text-danger'>Error when trying to save <b>conditions.csv</b> - is the file open on your computer?</span>"
        eel.python_bootbox(python_message)
    finally:
        print("moving on")


    for this_proc in experiment_json["python_procs"].keys():
        python_message = python_message + "...<br> saving the procedure <b>" + this_proc + "</b>"
        eel.python_bootbox(python_message)
        print(this_proc)
        try:
            this_proc_file = open("web/User/Experiments/" + experiment_name + "/" + this_proc, "w", newline='')
            this_proc_file.write(experiment_json["python_procs"][this_proc])
        except:
            errors += "...<br><span class='text-danger'>Error when trying to save <b>" + \
                              this_proc + \
                              "</b> - is the file open on your computer?</span>"
            eel.python_bootbox(python_message)
        finally:
            print("moving on")

    for this_stim in experiment_json["python_stims"].keys():
        python_message = python_message + "...<br> saving the stimuli <b>" + this_stim + "</b>"
        eel.python_bootbox(python_message)
        print(this_stim)
        try:
            this_stim_file = open("web/User/Experiments/" + experiment_name + "/" + this_stim, "w", newline='')
            this_stim_file.write(experiment_json["python_stims"][this_stim])
        except:
            print("error here");
            errors += "...<br><span class='text-danger'>Error when trying to save <b>" + \
                              this_stim + \
                              "</b> - is the file open on your computer?</span>"
            eel.python_bootbox(python_message)
        finally:
            print("moving on")

    if errors == "":
        eel.python_hide_bb()
    else:
        eel.python_bootbox(python_message + errors)

@eel.expose
def save_master_json(master_json):
    #detect if the "User" folder exists yet
    if os.path.isdir("web/User") == False:
        os.mkdir("web/User")
    master_file = open("web/User/master.json", "w")
    master_file.write(json.dumps(master_json))

@eel.expose
def save_survey(survey_name,
                survey_content):
    #detect if the "User" folder exists yet
    if os.path.isdir("web/User/Surveys") == False:
        os.mkdir("web/User/Surveys")
    survey_file = open("web/User/Surveys/" + survey_name, "w", newline='')
    survey_file.write(survey_content)


####################
# Start Collector ##
####################

if os.path.isdir("web") == False:

    # more code here



    # check if github is installed

    pull_open_collector_only()



eel.init('web') #allowed_extensions=[".js",".html"]


'''
for proc in process_iter():
    for conns in proc.get_connections(kind='inet'):
        if conns.laddr[1] == 8000:
            proc.send_signal(SIGKILL)
            continue
'''
eel.start('kitten/index.html', port=8000)

=======
    print("trying to save experiment")
    if os.path.isdir("web/Local/Experiments") == False:
        os.mkdir("web/Local/Experiments")
    print(experiment_name)
    print(json.dumps(experiment_json))
    experiment_file = open("web/Local/Experiments/" + experiment_name + ".json", "w")
    experiment_file.write(json.dumps(experiment_json))


@eel.expose
def save_master_json(master_json):
    #detect if the "Local" folder exists yet
    if os.path.isdir("web/Local") == False:
        os.mkdir("web/Local")
    master_file = open("web/Local/master.json", "w")
    master_file.write(json.dumps(master_json))

eel.init('web') #allowed_extensions=[".js",".html"]
eel.start('kitten/index.html')
>>>>>>> 94b84c7904574e43669bbc18777b7c6e9dc17b0f
