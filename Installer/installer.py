import os
import github
import pygit2
from tkinter import filedialog
from tkinter import *

def install_in_dir(this_dir):
    if this_dir != "":
        #Clone the newly created repo
        print(this_dir);
        repoClone = pygit2.clone_repository("https://github.com/open-collector/open-collector",
                                            str(this_dir) +
                                            "/Collector")
        os.chdir(this_dir + "/Collector");
        os.system("python -m eel Collector.py web --noconsole --icon=collector.ico --noconfirm")
        os.chdir("Updater")
        os.system("python -m eel UpdateCollector.py web --icon=collector.ico --noconfirm --onefile") #--noconsole
        
  



def browse_button():
    # Allow user to select a directory and store it in global var
    # called folder_path
    global folder_path
    filename = filedialog.askdirectory()
    folder_path.set(filename)
    print(filename)
    install_in_dir(filename);
    root.destroy


root = Tk()
folder_path = StringVar()
lbl1 = Label(master=root,textvariable=folder_path)
lbl1.grid(row=0, column=1)
button2 = Button(text="Click here to identify where you want to install Collector", command=browse_button)
button2.grid(row=0, column=3)

mainloop()