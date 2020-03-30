python -m eel Collector.py web --onefile --noconsole --icon=collector.ico
copy dist\Collector.exe Collector.exe
@RD /S /Q build
@RD /S /Q dist