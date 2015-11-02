Contributing to PrestaShop
--------------------------

PrestaShop is an open-source e-commerce solution. Everyone is welcome and even encouraged to contribute with their own improvements.

PrestaShop 1.6 is written mostly in PHP. Other languages used throughout are JavaScript, HTML, CSS, the Smarty templating language, SQL, and XML.

To contribute to the project, you should ideally be familiar with Git, the source code management system that PrestaShop uses, with the official repository being hosted on Github: 
* You can learn more about Git here: http://try.github.io/ (there are many tutorials available on the Web).
* You can get help on Github here: https://help.github.com/.
* Windows users can get a nice interface for Git by installing TortoiseGit: http://code.google.com/p/tortoisegit/

Contributors should follow the following process:

1. Create your Github account, if you do not have one already.
2. Fork the PrestaShop project to your Github account.
3. Clone your fork to your local machine. Be sure to make a recursive clone (use "git clone --recursive git://github.com/username/PrestaShop.git" or check the "Recursive" box in TortoiseGit) in order to have all the PrestaShop modules cloned too!
4. Create a branch in your local clone for your changes.
5. Change the files in your branch. Be sure to follow [the coding standards][1].
6. Push your changed branch to your fork in your Github account.
7. Create a pull request for your changes on the PrestaShop project. Be sure to follow [the commit message norm][2] in your pull request. If you need help to make a pull request, read the [Github help page about creating pull requests][3].
8. Wait for one of the core developers either to include your change in the codebase, or to comment on possible improvements you should make to your code.

That's it: you have contributed to this open source project! Congratulations!

The PrestaShop documentation features a thorough explanation of the [complete process to your first pull request][4].

If you don't feel comfortable forking the project or using Git, you can also either:
* Edit a file directly within Github: browse to the target file, click the "Edit" button, make your changes in the editor then click on "Propose File Change". Github will automatically create a new fork and branch on your own Github account, then suggest to create a pull request to PrestaShop. Once the pull request is submitted, you just have to wait for a core developer to answer you.
* Submit an issue using the Forge: [PrestaShop Forge][5] is the official ticket-tracker for PrestaShop, and the best place to write a bug ticket or request an improvement, while not having to be a developer at all. You will need to create an account on the Forge: [follow these instructions][6], then wait for a core developer to answer you.

Thank you for your help in making PrestaShop even better!


### About licenses

* All core files you commit in your pull request must respect/use the [Open Software License (OSL 3.0)][7].
* All modules files you commit in your pull request must respect/use the [Academic Free License (AFL 3.0)][8].


[1]: http://doc.prestashop.com/display/PS16/Coding+Standards
[2]: http://doc.prestashop.com/display/PS16/How+to+write+a+commit+message
[3]: https://help.github.com/articles/using-pull-requests
[4]: http://doc.prestashop.com/display/PS16/Contributing+code+to+PrestaShop
[5]: http://forge.prestashop.com/
[6]: http://doc.prestashop.com/display/PS16/How+to+use+the+Forge+to+contribute+to+PrestaShop
[7]: http://opensource.org/licenses/OSL-3.0
[8]: http://opensource.org/licenses/AFL-3.0

