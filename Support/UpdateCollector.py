######################################################################
# Update Collector from www.github.com/open-collector/open-collector #
# By Dr. Anthony Haffey                                              #
######################################################################

import os
import pygit2

#Copy user folder somewhere safe

if os.path.isdir("update_backup") == False:
    os.mkdir("update_backup")

try:
    copy_tree("web/User", "/update_backup/")
except:
    print("no user files yet")
finally:
    print("User files updated");

#Download open-collector
repoClone = pygit2.clone_repository("https://github.com/open-collector/open-collector",
                                   "../Collector-update")

#delete web folder
shutil.rmtree("web")
os.mkdir("web")    
copy_tree("../Collector-update/web","web")

#reinstate the User folder
if os.path.isdir("web/User") == False:
    os.mkdir("web/User")
copy_tree("update_backup", 
          "web/User")

shutil.copyfile("../Collector-update/Collector.py",
                "Collector.py")
                
shutil.copyfile("../Collector-update/UpdateCollector.py",
                "UpdateCollector.py")

os.system("python -m eel Collector.py web --noconsole --icon=collector.ico --noconfirm")


print("update complete")


os.system('rmdir /S /Q "{}"'.format(path_appendage + "../Collector-update"))

print("removed Collector-update")