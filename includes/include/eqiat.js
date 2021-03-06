
// tinyMCE stuff

qtitinymceoptions = {
	script_url: "/Wiki/includes/include/tiny_mce/tiny_mce.js",
	theme: "advanced",
	plugins: "safari,table,iespell,inlinepopups,contextmenu,paste,xhtmlxtras,template",
	theme_advanced_buttons1: "bold,italic,|,sub,sup,|,formatselect",
	theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink",
	theme_advanced_buttons3: "tablecontrols",
	theme_advanced_buttons4: "undo,redo,|,hr,charmap,image,template,|,iespell,removeformat,cleanup,code",
	theme_advanced_toolbar_location: "external",
	theme_advanced_statusbar_location: "bottom",
	valid_elements: "@[id|class|lang|label],"
		+ "a[href|type],"
		+ "strong/b,em/i,"
		+ "#p,-ol,-ul,-li,br,"
		+ "img[!src|!alt=|longdesc|height|width],"
		+ "-sub,-sup,"
		+ "-blockquote[cite],"
		+ "-table[summary],tr,"
		+ "tbody,thead,tfoot,"
		+ "#td[headers|scope|abbr|axis|rowspan|colspan],"
		+ "#th[headers|scope|abbr|axis|rowspan|colspan],"
		+ "caption,-div,"
		+ "-span,-code,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr,"
		+ "dd,dl,dt,cite,abbr,acronym,"
		+ "object[!data|!type|width|height],param[!name|!value|!valuetype|type],"
		+ "col[span],colgroup,"
		+ "dfn,kbd,"
		+ "q[cite],samp,small,"
		+ "tt,var,big",
	entity_encoding: "raw",
	content_css: "/Wiki/includes/include/tinymce.css"
};
$(document).ready(function() {
	$("textarea.qtitinymce").focus(focustinymce);
	qtitinymceoptions.theme_advanced_resizing = $("#stimulus").is(".resizable");
	$("#stimulus").tinymce(qtitinymceoptions);
});
focustinymce = function() {
	if (typeof tinyMCE != "undefined" && tinyMCE.get($(this).attr("id")))
		return;

	removetinymces();
	qtitinymceoptions.theme_advanced_resizing = $(this).is(".resizable");
	$(this).tinymce(qtitinymceoptions);
};
removetinymces = function(obj) {
	if (typeof obj == "undefined")
		var obj = $("textarea.qtitinymce");
	obj.each(function() {
		if (typeof tinyMCE != "undefined" && tinyMCE.get($(this).attr("id")))
			tinyMCE.execCommand("mceRemoveControl", false, $(this).attr("id"));
	});
};

// scrolling options
scrollduration = 0;
scrolloptions = {
	offset: -50
};

// check edit item form
edititemsubmitcheck = function() {
	// do any pre-check logic
	if (typeof edititemsubmitcheck_pre == "function")
		edititemsubmitcheck_pre();

	// clear any previously set background colours
	$("input, textarea").removeClass("error warning");
	$("#stimulus_ifr").contents().find("body").removeClass("error warning");

	// common errors

	// title must be set
	if ($("#title").val().length == 0) {
		$.scrollTo($("#title").addClass("error"), scrollduration, scrolloptions);
		alert("A title must be set for this item");
		return false;
	}

	// item-specific errors
	if (typeof edititemsubmitcheck_itemspecificerrors == "function" && !edititemsubmitcheck_itemspecificerrors())
		return false;

	// common warnings

	// confirm the user wanted an empty stimulus
	if ($("#stimulus").val().length == 0) {
		if ($("#stimulus_parent").is(":visible")) {
			$("#stimulus_ifr").contents().find("body").addClass("warning");
			$.scrollTo($("#stimulus_parent").addClass("warning"), scrollduration, scrolloptions);
		} else
			$.scrollTo($("#stimulus").addClass("warning"), scrollduration, scrolloptions);
		if (!confirm("Stimulus is empty -- click OK to continue regardless or cancel to edit it"))
			return false;
		else {
			$("#stimulus").removeClass("error warning");
			$("#stimulus_ifr").contents().find("body").removeClass("error warning");
		}
	}

	// item-specific warnings
	if (typeof edititemsubmitcheck_itemspecificwarnings == "function" && !edititemsubmitcheck_itemspecificwarnings())
		return false;

	return true;
}
$(document).ready(function() {
	$("#edititemsubmit").click(edititemsubmitcheck);
});

