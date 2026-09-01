
qlovisualinspection.md suggests a folder structure that under /project there would be a qlovisualinspection folder and a python server. 

For better responsability-segregation, I decided to let the python server be a folder on its own, outside modules; and i decided to make qlovisualinspection php module inside modules folder to better alignment with project pattern.

So the folder structure I decided is:

project /
--
-- modules/
---- ...
---- qlovisualinspection/
---- ...
--
--
-- inspection-service-python/
---- ...





