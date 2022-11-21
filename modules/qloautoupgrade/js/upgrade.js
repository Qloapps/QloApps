if (typeof input === 'undefined') {
  var input = {
    manualMode: "",
    _PS_MODE_DEV_: true,
    PS_AUTOUP_BACKUP: true,
    adminUrl: "http://test.com/admin",
    adminDir: "/admin",
    token: "asdadsasdasdasd",
    txtError: [],
    firstTimeParams: {},
    ajaxUpgradeTabExists: true,
    currentIndex: 'page.php',
    tab: input.tab,
    channel: 'major',
    translation: {
      confirmDeleteBackup: "Are you sure you want to delete this backup?",
      "delete": "Delete",
      updateInProgress: "An update is currently in progress... Click \"OK\" to abort.",
      upgradingPrestaShop: "Upgrading PrestaShop",
      upgradeComplete: "Upgrade complete",
      upgradeCompleteWithWarnings: "Upgrade complete, but warning notifications has been found.",
      todoList: [
        "Cookies have changed, you will need to log in again once you refreshed the page",
        "Modules zips have been updated, open modules and services tab to finish module updation",
        "Javascript and CSS files have changed, please clear your browser cache with CTRL-F5",
        "Please check that your front-office theme is functional (try to create an account, place an order...)",
        "Product images do not appear in the front-office? Try regenerating the thumbnails in Preferences > Images",
        "Do not forget to reactivate your shop once you have checked everything!"
      ],
      todoListTitle: "ToDo list:",
      startingRestore: "Starting restoration...",
      restoreComplete: "Restoration complete.",
      cannotDownloadFile: "Your server cannot download the file. Please upload it first by ftp in your admin/autoupgrade directory",
      jsonParseErrorForAction: "Javascript error (parseJSON) detected for action ",
      manuallyGoToButton: "Manually go to %s button",
      endOfProcess: "End of process",
      processCancelledCheckForRestore: "Operation canceled. Checking for restoration...",
      confirmRestoreBackup: "Do you want to restore SomeBackupName?",
      processCancelledWithError: "Operation canceled. An error happened.",
      missingAjaxUpgradeTab: "[TECHNICAL ERROR] ajax-upgradetab.php is missing. Please reinstall the module.",
      clickToRefreshAndUseNewConfiguration: "Click to refresh the page and use the new configuration",
      errorDetectedDuring: "Error detected during",
      downloadTimeout: "The request exceeded the max_time_limit. Please change your server configuration.",
      seeOrHideList: "See or hide the list",
      coreFiles: "Core file(s)",
      mailFiles: "Mail file(s)",
      translationFiles: "Translation file(s)",
      linkAndMd5CannotBeEmpty: "Link and MD5 hash cannot be empty",
      needToEnterArchiveVersionNumber: "You need to enter the version number associated with the archive.",
      noArchiveSelected: "No archive has been selected.",
      needToEnterDirectoryVersionNumber: "You need to enter the version number associated with the directory.",
      confirmSkipBackup: "Please confirm that you want to skip the backup.",
      confirmPreserveFileOptions: "Please confirm that you want to preserve file options.",
      lessOptions: "Less options",
      moreOptions: "More options (Expert mode)",
      filesWillBeDeleted: "These files will be deleted",
      filesWillBeReplaced: "These files will be replaced",
    }
  };
}

console.log(input);
var firstTimeParams = input.firstTimeParams.nextParams;
firstTimeParams.firstTime = "1";

function ucFirst(str) {
  if (str.length > 0) {
    return str[0].toUpperCase() + str.substring(1);
  }
  return str;
}

function cleanInfo() {
  $("#infoStep").html("reset<br/>");
}

function updateInfoStep(msg) {
  if (msg) {
    var $infoStep = $("#infoStep");
    $infoStep.append(msg + "<div class=\"clear\"></div>");
    $infoStep.prop({scrollTop: $infoStep.prop("scrollHeight")}, 1);
  }
}

function addError(arrError) {
  if (typeof arrError !== "undefined" && arrError.length) {
    $("#errorDuringUpgrade").show();
    var $infoError = $("#infoError");
    for (var i = 0; i < arrError.length; i++) {
      $infoError.append(arrError[i] + "<div class=\"clear\"></div>");
    }
    // Note: jquery 1.6 makes use of prop() instead of attr()
    $infoError.prop({scrollTop: $infoError.prop("scrollHeight")}, 1);
  }
}

function addQuickInfo(arrQuickInfo) {
  if (arrQuickInfo) {
    var $quickInfo = $("#quickInfo");
    $quickInfo.show();
    for (var i = 0; i < arrQuickInfo.length; i++) {
      $quickInfo.append(arrQuickInfo[i] + "<div class=\"clear\"></div>");
    }
    // Note : jquery 1.6 make uses of prop() instead of attr()
    $quickInfo.prop({scrollTop: $quickInfo.prop("scrollHeight")}, 1);
  }
}

// js initialization : prepare upgrade and rollback buttons
$(document).ready(function(){

  $(".nobootstrap.no-header-toolbar").removeClass("nobootstrap").addClass("bootstrap");

  $(document).on("click", "a.confirmBeforeDelete", function(e) {
    if (!confirm(input.translation.confirmDeleteBackup)) {
      e.preventDefault();
    }
  });

  $("select[name=channel]").change(function(e) {
    $(this).find("option").each(function() {
      var $this = $(this);
        $("#for-" + $this.attr("id"))
          .toggle($this.is(":selected"));
    });

    refreshChannelInfos();
  });

  function refreshChannelInfos() {
    var val = $("select[name=channel]").val();
    $.ajax({
      type: "POST",
      url: input.adminUrl + "/qloautoupgrade/ajax-upgradetab.php",
      async: true,
      data: {
        dir: input.adminDir,
        token: input.token,
        tab: input.tab,
        action: "getChannelInfo",
        ajaxMode: "1",
        params: {channel: val}
      },
      success: function(res, textStatus, jqXHR) {
        if (isJsonString(res)) {
          res = $.parseJSON(res);
        } else {
          res = {nextParams: {status: "error"}};
        }

        var answer = res.nextParams.result;
        if (typeof answer !== "undefined") {
          var $channelInfos = $("#channel-infos");
          $channelInfos.replaceWith(answer.div);
          if (answer.available) {
            $("#channel-infos .all-infos").show();
          } else {
            $channelInfos.html(answer.div);
            $("#channel-infos .all-infos").hide();
          }
        }
      },
      error: function(res, textStatus, jqXHR) {
        if (textStatus === "timeout" && action === "download") {
          updateInfoStep(input.translation.cannotDownloadFile);
        }
        else {
          // technical error : no translation needed
          $("#checkPrestaShopFilesVersion").html("<img src=\"../img/admin/warning.gif\" /> Error Unable to check md5 files");
        }
      }
    });
  }

  // the following prevents to leave the page at the inappropriate time
  $.xhrPool = [];
  $.xhrPool.abortAll = function() {
    $.each(this, function(jqXHR) {
      if (jqXHR && (jqXHR.readystate !== 4)) {
        jqXHR.abort();
      }
    });
  };

  $(".upgradestep").click(function(e) {
    e.preventDefault();
    // $.scrollTo("#options")
  });

  // set timeout to 120 minutes (before aborting an ajax request)
  $.ajaxSetup({timeout:7200000});

  // prepare available button here, without params ?
  console.log('firstTimeParams');
  console.log(firstTimeParams);
  prepareNextButton("#upgradeNow",firstTimeParams);

  /**
   * reset rollbackParams js array (used to init rollback button)
   */
  $("select[name=restoreName]").change(function() {
    var val = $(this).val();

    // show delete button if the value is not 0
    if (val != 0) {
      $("span#buttonDeleteBackup").html(
        "<br><a class=\"button confirmBeforeDelete\" href=\"index.php?tab=AdminSelfUpgrade&token="
        + input.token
        + "&amp;deletebackup&amp;name="
        + $(this).val()
        + "\"><img src=\"../img/admin/disabled.gif\" />"
        + input.translation.delete
        + "</a>"
      );
    }

    if (val != 0) {
      $("#rollback").removeAttr("disabled");
      var rollbackParams = $.extend(true, {}, firstTimeParams);

      delete rollbackParams.backupName;
      delete rollbackParams.backupFilesFilename;
      delete rollbackParams.backupDbFilename;
      delete rollbackParams.restoreFilesFilename;
      delete rollbackParams.restoreDbFilenames;

      // init new name to backup
      rollbackParams.restoreName = val;
      prepareNextButton("#rollback", rollbackParams);
      // Note : theses buttons have been removed.
      // they will be available in a future release (when DEV_MODE and MANUAL_MODE enabled)
      // prepareNextButton("#restoreDb", rollbackParams);
      // prepareNextButton("#restoreFiles", rollbackParams);
    } else {
      $("#rollback").attr("disabled", "disabled");
    }
  });

  $("div[id|=for]").hide();
  $("select[name=channel]").change();

  if (!input.ajaxUpgradeTabExists) {
    $("#checkPrestaShopFilesVersion").html("<img src=\"../img/admin/warning.gif\" />" + input.translation.missingAjaxUpgradeTab);
  }
});

function showConfigResult(msg, type) {
  if (!type) {
    type = "conf";
  }
  var $configResult = $("#configResult");
  $configResult.html("<div class=\"" + type + "\">" + msg + "</div>").show();

  if (type === "conf") {
    $configResult.delay(3000).fadeOut("slow", function() {
      location.reload();
    });
  }
}

// reuse previousParams, and handle xml returns to calculate next step
// (and the correct next param array)
// a case has to be defined for each requests that returns xml
function afterUpdateConfig(res) {
  var params = res.nextParams;
  var config = params.config;
  var $oldChannel = $("select[name=channel] option.current");

  if (config.channel != $oldChannel.val()) {
    var $newChannel = $("select[name=channel] option[value=" + config.channel + "]");
    $oldChannel
      .removeClass("current")
      .html($oldChannel.html().substr(2));

    $newChannel
      .addClass("current")
      .html("* " + $newChannel.html());
  }

  if (res.error == 1) {
    showConfigResult(res.next_desc, "error");
  } else {
    showConfigResult(res.next_desc);
  }

  $("#upgradeNow")
    .unbind()
    .replaceWith(
      "<a class=\"button-autoupgrade\" href=\""
      + input.currentIndex
      + "&token="
      + input.token
      + "\" >"
      + input.translation.clickToRefreshAndUseNewConfiguration
      + "</a>"
  );
}

function startProcess(type) {

  // hide useless divs, show activity log
  $("#informationBlock,#comparisonBlock,#currentConfigurationBlock,#backupOptionsBlock,#upgradeOptionsBlock,#upgradeButtonBlock").slideUp("fast");
  $(".autoupgradeSteps a").addClass("button");
  $("#activityLogBlock").fadeIn("slow");

  $(window).bind("beforeunload", function(e) {
    if (confirm(input.translation.updateInProgress)) {
      $.xhrPool.abortAll();
      $(window).unbind("beforeunload");
      return true;
    } else {
      if (type === "upgrade") {
        e.returnValue = false;
        e.cancelBubble = true;
        if (e.stopPropagation) {
          e.stopPropagation();
        }
        if (e.preventDefault) {
          e.preventDefault();
        }
      }
    }
  });
}

function afterUpgradeNow(res) {
  startProcess("upgrade");
  $("#upgradeNow")
    .unbind()
    .replaceWith(
      "<span id=\"upgradeNow\" class=\"button-autoupgrade\">"
      + input.translation.upgradingPrestaShop
      + " ...</span>"
    );
}

function afterUpgradeComplete(res) {
  var params = res.nextParams;

  $("#pleaseWait").hide();
  if (params.warning_exists == "false") {
    $("#upgradeResultCheck")
      .html("<p>" + input.translation.upgradeComplete + "</p>")
      .show();
    $("#infoStep").html("<p class=\"alert alert-success\">" + input.translation.upgradeComplete + "</p>");
  }
  else {
    params = res.nextParams;
    $("#pleaseWait").hide();
    $("#upgradeResultCheck")
      .html("<p>" + input.translation.upgradeCompleteWithWarnings + "</p>")
      .show("slow");
    $("#infoStep").html("<p class=\"alert alert-warning\">" + input.translation.upgradeCompleteWithWarnings + "</p>");
  }

  var todoList = input.translation.todoList;
  var todoBullets = "<ul>";
  for (var i in todoList) {
    todoBullets += "<li>" + todoList[i] + "</li>";
  }

  todoBullets += "</ul>";

  $("#upgradeResultToDoList")
    .html("<strong>" + input.translation.todoListTitle + "</strong>")
    .append(todoBullets)
    .show();

  $(window).unbind("beforeunload");
}

function afterError(res) {
  var params = res.nextParams;
  if (params.next === "") {
    $(window).unbind("beforeunload");
  }
  $("#pleaseWait").hide();

  addQuickInfo(["unbind :) "]);
}

function afterRollback(res) {
  startProcess("rollback");
}

function afterRollbackComplete(res) {
  var params = res.nextParams;
  $("#pleaseWait").hide();
  $("#upgradeResultCheck")
    .html("<p>" + input.translation.restoreComplete + "</p>")
    .show("slow");
  updateInfoStep("<p class=\"alert alert-success\">" + input.translation.restoreComplete + "</p>");
  $(window).unbind();
}

function afterRestoreDb(params) {
  // $("#restoreBackupContainer").hide();
}

function afterRestoreFiles(params) {
  // $("#restoreFilesContainer").hide();
}

function afterBackupFiles(res) {
  var params = res.nextParams;
  // if (params.stepDone)
}

/**
 * afterBackupDb display the button
 */
function afterBackupDb(res) {
  var params = res.nextParams;

  if (res.stepDone && input.PS_AUTOUP_BACKUP === true) {
    $("#restoreBackupContainer").show();
    $("select[name=restoreName]")
      .append("<option selected=\"selected\" value=\"" + params.backupName + "\">" + params.backupName + "</option>")
      .val('')
      .change();
  }
}

function call_function(func) {
  this[func].apply(this, Array.prototype.slice.call(arguments, 1));
}

function doAjaxRequest(action, nextParams) {
  if (input._PS_MODE_DEV_ === true) {
    addQuickInfo(["[DEV] ajax request : " + action]);
  }
  $("#pleaseWait").show();
  var req = $.ajax({
    type: "POST",
    url: input.adminUrl + "/qloautoupgrade/ajax-upgradetab.php",
    async: true,
    data: {
      dir: input.adminDir,
      ajaxMode: "1",
      token: input.token,
      tab: input.tab,
      action: action,
      params: nextParams
    },
    beforeSend: function(jqXHR) {
      $.xhrPool.push(jqXHR);
    },
    complete: function(jqXHR) {
      // just remove the item to the "abort list"
      $.xhrPool.pop();
      // $(window).unbind("beforeunload");
    },
    success: function(res, textStatus, jqXHR) {
      $("#pleaseWait").hide();
      try {
        res = $.parseJSON(res);
      }
      catch (e) {
        res = {status: "error", nextParams: nextParams};
        alert(
          input.translation.jsonParseErrorForAction
          + action
          + "\"" + input.translation.startingRestore + "\""
        );
      }

      addQuickInfo(res.nextQuickInfo);
      addError(res.nextErrors);
      updateInfoStep(res.next_desc);
      var currentParams = res.nextParams;
      if (res.status === "ok") {
        $("#" + action).addClass("done");
        if (res.stepDone) {
          $("#" + action).addClass("stepok");
        }
        // if a function "after[action name]" exists, it should be called now.
        // This is used for enabling restore buttons for example
        var funcName = "after" + ucFirst(action);
        if (typeof window[funcName] === "function") {
          call_function(funcName, res);
        }

        handleSuccess(res, action);
      } else {
        // display progression
        $("#" + action).addClass("done steperror");
        var validActions = [
          "rollback",
          "rollbackComplete",
          "restoreFiles",
          "restoreDb",
          "rollback",
          "noRollbackFound"
        ];
        if (validActions.indexOf(action) === -1) {
          handleError(res, action);
        } else {
          alert(input.translation.errorDetectedDuring + " [" + action + "].");
        }
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      $("#pleaseWait").hide();
      if (textStatus === "timeout") {
        if (action === "download") {
          updateInfoStep(input.translation.cannotDownloadFile);
        } else {
          updateInfoStep("[Server Error] Timeout: " + input.translation.downloadTimeout);
        }
      }
      else {
          try {
            res = $.parseJSON(jqXHR.responseText);
            addQuickInfo(res.nextQuickInfo);
            addError(res.nextErrors);
            updateInfoStep(res.next_desc);
          }
          catch (e) {
            updateInfoStep("[Ajax / Server Error for action " + action + "] textStatus: \"" + textStatus + " \" errorThrown:\"" + errorThrown + " \" jqXHR: \" " + jqXHR.responseText + "\"");
          }
      }
    }
  });
  return req;
}

/**
 * prepareNextButton make the button button_selector available, and update the nextParams values
 *
 * @param button_selector $button_selector
 * @param nextParams $nextParams
 * @return void
 */
function prepareNextButton(button_selector, nextParams) {
  $(button_selector)
    .unbind()
    .click(function(e) {
      e.preventDefault();
      $("#currentlyProcessing").show();
      var action = button_selector.substr(1);
      doAjaxRequest(action, nextParams);
    });
}

/**
 * handleSuccess
 * res = {error:, next:, next_desc:, nextParams:, nextQuickInfo:,status:"ok"}
 * @param res $res
 * @return void
 */
function handleSuccess(res, action) {
  if (res.next !== "") {

    $("#" + res.next).addClass("nextStep");
    var validActions = [
      "rollback",
      "rollbackComplete",
      "restoreFiles",
      "restoreDb",
      "rollback",
      "noRollbackFound"
    ];
    if (input.manualMode && validActions.indexOf(action) === -1) {
      prepareNextButton("#" + res.next, res.nextParams);
      alert(input.translation.manuallyGoToButton.replace("%s", res.next));
    } else {
      // if next is rollback, prepare nextParams with rollbackDbFilename and rollbackFilesFilename
      if (res.next === "rollback") {
        res.nextParams.restoreName = "";
      }
      doAjaxRequest(res.next, res.nextParams);
      // 2) remove all step link (or show them only in dev mode)
      // 3) when steps link displayed, they should change color when passed if they are visible
    }
  } else {
    // Way To Go, end of upgrade process
    addQuickInfo([input.translation.endOfProcess]);
  }
}

// res = {nextParams, next_desc}
function handleError(res, action) {
  // display error message in the main process thing
  // In case the rollback button has been deactivated, just re-enable it
  $("#rollback").removeAttr("disabled");
  // auto rollback only if current action is upgradeFiles or upgradeDb
  var validActions = [
    "upgradeFiles",
    "upgradeDb",
    "upgradeModules"
  ];
  if (validActions.indexOf(action) !== -1) {
    $(".button-autoupgrade").html(input.translation.processCancelledCheckForRestore);
    res.nextParams.restoreName = res.nextParams.backupName;
    if (confirm(input.translation.confirmRestoreBackup)) {
      doAjaxRequest("rollback", res.nextParams);
    }
  } else {
    $(".button-autoupgrade").html(input.translation.processCancelledWithError);
    $(window).unbind();
  }
}

// ajax to check md5 files
function addModifiedFileList(title, fileList, css_class, container) {
  var subList = $("<ul class=\"changedFileList " + css_class + "\"></ul>");

  $(fileList).each(function(k, v) {
    $(subList).append("<li>" + v + "</li>");
  });

  $(container)
    .append("<h3><a class=\"toggleSublist\" href=\"#\" >" + title + "</a> (" + fileList.length + ")</h3>")
    .append(subList)
    .append("<br/>");
}

// -- Should be executed only if ajaxUpgradeTabExists

function isJsonString(str) {
  try {
    typeof str !== "undefined" && JSON.parse(str);
  } catch (e) {
    return false;
  }
  return true;
}

$(document).ready(function() {
  $.ajax({
    type: "POST",
    url: input.adminUrl + "/qloautoupgrade/ajax-upgradetab.php",
    async: true,
    data: {
      dir: input.adminDir,
      token: input.token,
      tab: input.tab,
      action: "checkFilesVersion",
      ajaxMode: "1",
      params: {}
    },
    success: function(res, textStatus, jqXHR) {
      if (isJsonString(res)) {
        res = $.parseJSON(res);
      } else {
        res = {nextParams: {status: "error"}};
      }
      var answer = res.nextParams;
      var $checkPrestaShopFilesVersion = $("#checkPrestaShopFilesVersion");

      $checkPrestaShopFilesVersion.html("<span> " + answer.msg + " </span> ");
      if (answer.status === "error" || (typeof answer.result  === "undefined")) {
        $checkPrestaShopFilesVersion.prepend("<img src=\"../img/admin/warning.gif\" /> ");
      } else {
        $checkPrestaShopFilesVersion
          .prepend("<img src=\"../img/admin/warning.gif\" /> ")
          .append("<a id=\"toggleChangedList\" class=\"button\" href=\"\">" + input.translation.seeOrHideList + "</a><br/>")
          .append("<div id=\"changedList\" style=\"display:none \"><br/>");

        if (answer.result.core.length) {
          addModifiedFileList(input.translation.coreFiles, answer.result.core, "changedImportant", "#changedList");
        }
        if (answer.result.mail.length) {
          addModifiedFileList(input.translation.mailFiles, answer.result.mail, "changedNotice", "#changedList");
        }
        if (answer.result.translation.length) {
          addModifiedFileList(input.translation.translationFiles, answer.result.translation, "changedNotice", "#changedList");
        }

        $("#toggleChangedList").bind("click", function(e) {
          e.preventDefault();
          $("#changedList").toggle();
        });

        $(".toggleSublist").die().live("click", function(e) {
          e.preventDefault();
          $(this).parent().next().toggle();
        });
      }
    },
    error: function(res, textStatus, jqXHR) {
      if (textStatus === "timeout" && action === "download") {
        updateInfoStep(input.translation.cannotDownloadFile);
      } else {
        // technical error : no translation needed
        $("#checkPrestaShopFilesVersion").html("<img src=\"../img/admin/warning.gif\" /> Error: Unable to check md5 files");
      }
    }
  });

  $.ajax({
    type: "POST",
    url: input.adminUrl + "/qloautoupgrade/ajax-upgradetab.php",
    async: true,
    data: {
      dir: input.adminDir,
      token: input.token,
      tab: input.tab,
      action: "compareReleases",
      ajaxMode: "1",
      params: {}
    },
    success: function(res, textStatus, jqXHR) {
      if (isJsonString(res)) {
        res = $.parseJSON(res);
      } else {
        res = {nextParams: {status: "error"}};
      }
      var answer = res.nextParams;
      var $checkPrestaShopModifiedFiles = $("#checkPrestaShopModifiedFiles");

      $checkPrestaShopModifiedFiles.html("<span> " + answer.msg + " </span> ");
      if (answer.status === "error" || typeof answer.result === "undefined") {
        $checkPrestaShopModifiedFiles.prepend("<img src=\"../img/admin/warning.gif\" /> ");
      } else {
        $checkPrestaShopModifiedFiles
          .prepend("<img src=\"../img/admin/warning.gif\" /> ")
          .append("<a id=\"toggleDiffList\" class=\"button\" href=\"\">"+input.translation.seeOrHideList+"</a><br/>")
          .append("<div id=\"diffList\" style=\"display:none \"><br/>");

        if (answer.result.deleted.length) {
          addModifiedFileList(input.translation.filesWillBeDeleted, answer.result.deleted, "diffImportant", "#diffList");
        }
        if (answer.result.modified.length) {
          addModifiedFileList(input.translation.filesWillBeReplaced, answer.result.modified, "diffImportant", "#diffList");
        }

        $("#toggleDiffList").bind("click", function(e) {
          e.preventDefault();
          $("#diffList").toggle();
        });

        $(".toggleSublist").die().live("click", function(e) {
          e.preventDefault();
          // this=a, parent=h3, next=ul
          $(this).parent().next().toggle();
        });
      }
    },
    error: function(res, textStatus, jqXHR) {
      if (textStatus === "timeout" && action === "download") {
        updateInfoStep(input.translation.cannotDownloadFile);
      } else {
        // technical error : no translation needed
        $("#checkPrestaShopFilesVersion").html("<img src=\"../img/admin/warning.gif\" /> Error: Unable to check md5 files");
      }
    }
  });
});

// -- END

// advanced/normal mode
function switch_to_advanced(){
  $("input[name=btn_adv]").val(input.translation.lessOptions);
  $("#advanced").show();
}

function switch_to_normal(){
  $("input[name=btn_adv]").val(input.translation.moreOptions);
  $("#advanced").hide();
}

$("input[name=btn_adv]").click(function(e) {
  if ($("#advanced:visible").length) {
    switch_to_normal();
  } else {
    switch_to_advanced();
  }
});

$(document).ready(function(){
  if (input.channel === 'major') {
    switch_to_normal();
  } else {
    switch_to_advanced();
  }
});

$(document).ready(function() {
  $("input[name|=submitConf]").bind("click", function(e) {
    var params = {};
    var $newChannel = $("select[name=channel] option:selected").val();
    var $oldChannel = $("select[name=channel] option.current").val();
    $oldChannel = "";

    if ($oldChannel != $newChannel) {
      var validChannels = [
        "major",
        "minor",
        "rc",
        "beta",
        "alpha"
      ];
      if (validChannels.indexOf($newChannel) !== -1) {
        params.channel = $newChannel;
      }

      if ($newChannel === "private") {
        if (($("input[name=private_release_link]").val() == "") || ($("input[name=private_release_md5]").val() == "")) {
          showConfigResult(input.translation.linkAndMd5CannotBeEmpty, "error");
          return false;
        }
        params.channel = "private";
        params.private_release_link = $("input[name=private_release_link]").val();
        params.private_release_md5 = $("input[name=private_release_md5]").val();
        if ($("input[name=private_allow_major]").is(":checked")) {
          params.private_allow_major = 1;
        } else {
          params.private_allow_major = 0;
        }
      } else if ($newChannel === "archive") {
        var archive_prestashop = $("select[name=archive_prestashop]").val();
        var archive_num = $("input[name=archive_num]").val();
        if (archive_num == "") {
          showConfigResult(input.translation.needToEnterArchiveVersionNumber, "error");
          return false;
        }
        if (archive_prestashop == "") {
          showConfigResult(input.translation.noArchiveSelected, "error");
          return false;
        }
        params.channel = "archive";
        params.archive_prestashop = archive_prestashop;
        params.archive_num = archive_num;
      } else if ($newChannel === "directory") {
        params.channel = "directory";
        params.directory_prestashop = $("select[name=directory_prestashop] option:selected").val();
        var directory_num = $("input[name=directory_num]").val();
        if (directory_num == "" || directory_num.indexOf(".") == -1) {
          showConfigResult(input.translation.needToEnterDirectoryVersionNumber, "error");
          return false;
        }
        params.directory_num = $("input[name=directory_num]").val();
      }
    }
    // note: skipBackup is currently not used
    if ($(this).attr("name") == "submitConf-skipBackup") {
      var skipBackup = $("input[name=submitConf-skipBackup]:checked").length;
      if (skipBackup == 0 || confirm(input.translation.confirmSkipBackup)) {
        params.skip_backup = $("input[name=submitConf-skipBackup]:checked").length;
      } else {
        $("input[name=submitConf-skipBackup]:checked").removeAttr("checked");
        return false;
      }
    }

    // note: preserveFiles is currently not used
    if ($(this).attr("name") == "submitConf-preserveFiles") {
      var preserveFiles = $("input[name=submitConf-preserveFiles]:checked").length;
      if (confirm(input.translation.confirmPreserveFileOptions)) {
        params.preserve_files = $("input[name=submitConf-preserveFiles]:checked").length;
      } else {
        $("input[name=submitConf-skipBackup]:checked").removeAttr("checked");
        return false;
      }
    }
    var res = doAjaxRequest("updateConfig", params);
  });
});
