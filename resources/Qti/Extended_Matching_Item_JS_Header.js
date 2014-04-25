alphaChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		addoption = function() {
			
			// clone the last option on the list and increment its id
			//alert('EMI JS');
			var newoption = $("#options tr.option:last").clone();
			var oldid = parseInt($("input.optiontext", newoption).attr("id").split("_")[1]);
			var newid = oldid + 1;

			// give it the new id number and wipe its text
			newoption.attr("id", "option_" + newid);
			$(".optionid", newoption).text(alphaChars.charAt(newid));
			$("input.optiontext", newoption).attr("id", "option_" + newid + "_optiontext").attr("name", "option_" + newid + "_optiontext").val("").removeClass("error warning");

			// stripe it
			newoption.removeClass("row" + (oldid % 2)).addClass("row" + (newid % 2));

			// reinstate the remove action
			$("input.removeoption", newoption).click(removeoption);

			// add it to the list
			$("#options").append(newoption);

			// add checkboxes for this new option to each question
			$("#questions tr.question td.correctresponses").each(function() {
				var newcorrect = $("label.correct:last", this).clone();
				var questionid = newcorrect.attr("id").split("_")[1];
				newcorrect.attr("id", "question_" + questionid + "_option_" + newid);
				$("input", newcorrect).removeAttr("checked").attr("id", "question_" + questionid + "_option_" + newid + "_correct").attr("name", "question_" + questionid + "_option_" + newid + "_correct");
				$(".optionid", newcorrect).text(alphaChars.charAt(newid));
				$(this).append(newcorrect);
			});
		};

		removeoption = function() {
			
			if ($("#options tr.option").size() < 2) {
				alert("Can't remove the last option");
				return;
			}

			var row = $(this).parents("tr:first");

			// get its id
			var optionid = row.attr("id").split("_")[1];

			// remove it
			row.remove();

			// renumber and restripe the remaining options
			var i = 0;
			$("#options tr.option").each(function() {
				$(this).attr("id", "option_" + i);
				$(".optionid", this).text(alphaChars.charAt(i));
				$("input.optiontext", this).attr("id", "option_" + i + "_optiontext").attr("name", "option_" + i + "_optiontext");
				$(this).removeClass("row" + ((i + 1) % 2)).addClass("row" + (i % 2));
				i++;
			});

			// remove this option's checkboxes from each question
			for (var i = 0; i < $("#questions tr.question").size(); i++) {
				$("#question_" + i + "_option_" + optionid).remove();
			}

			// renumber the remaining checkboxes
			$("#questions tr.question td.correctresponses").each(function() {
				var questionid = $(this).parents("tr.question:first").attr("id").split("_")[1];
				i = 0;
				$("label.correct", this).each(function() {
					$(this).attr("id", "question_" + questionid + "_option_" + i);
					$(".optionid", this).text(alphaChars.charAt(i));
					$("input.correct", this).attr("id", "question_" + questionid + "_option_" + i + "_correct").attr("name", "question_" + questionid + "_option_" + i + "_correct");
					i++;
				});
			});
		};

		addquestion = function() {
			// clone the last question on the list and increment its id
			var newquestion = $("#questions tr.question:last").clone();
			var oldid = parseInt($("textarea", newquestion).attr("id").split("_")[1]);
			var newid = oldid + 1;

			// give it the new id number and wipe its text
			newquestion.attr("id", "question_" + newid);
			$("textarea", newquestion).attr("id", "question_" + newid + "_prompt").attr("name", "question_" + newid + "_prompt").val("").removeClass("error warning");

			// clear all its checkboxes and update their question numbers
			$("input.correct", newquestion).removeAttr("checked");
			var i = 0;
			$("td.correctresponses label.correct", newquestion).each(function() {
				$(this).attr("id", "question_" + newid + "_option_" + i);
				$("input.correct", this).attr("id", "question_" + newid + "_option_" + i + "_correct").attr("name", "question_" + newid + "_option_" + i + "_correct");
				i++;
			});

			// stripe it
			newquestion.removeClass("row" + (oldid % 2)).addClass("row" + (newid % 2));

			// reinstate the remove action
			$("input.removequestion", newquestion).click(removequestion);

			// add it to the list
			$("#questions").append(newquestion);
		};

		removequestion = function() {
			if ($("#questions tr.question").size() < 2) {
				alert("Can't remove the last question");
				return;
			}

			$(this).parents("tr:first").remove();

			// renumber the remaining questions
			var i = 0;
			$("#questions tr.question").each(function() {
				$(this).attr("id", "question_" + i);
				$("textarea", this).attr("id", "question_" + i + "_prompt").attr("name", "question_" + i + "_prompt");
				var j = 0;
				$("td.correctresponses label.correct", this).each(function() {
					$(this).attr("id", "question_" + i + "_option_" + j);
					$("input.correct", this).attr("id", "question_" + i + "_option_" + j + "_correct").attr("name", "question_" + i + "_option_" + j + "_correct");
					j++;
				});
				$(this).removeClass("row" + ((i + 1) % 2)).addClass("row" + (i % 2));
				i++;
			});
		};

		edititemsubmitcheck_itemspecificwarnings = function() {
			// confirm the user wanted any empty boxes
			var ok = true;
			$("input.optiontext").each(function(n) {
				if ($(this).val().length == 0) {
					$.scrollTo($(this).addClass("warning"), scrollduration, scrolloptions);
					ok = confirm("Option " + (n + 1) + " is empty -- click OK to continue regardless or cancel to edit it");
					if (ok)
						$(this).removeClass("error warning");
					else
						return false; //this is "break" in the Jquery each() pseudoloop
				}
			});
			if (!ok) return false;
			$("textarea.prompt").each(function(n) {
				if ($(this).val().length == 0) {
					$.scrollTo($(this).addClass("warning"), scrollduration, scrolloptions);
					ok = confirm("The prompt for question " + (n + 1) + " is empty -- click OK to continue regardless or cancel to edit it");
					if (ok)
						$(this).removeClass("error warning");
					else
						return false; //this is "break" in the Jquery each() pseudoloop
				}
			});
			if (!ok) return false;

			// warn about any identical options
			for (var i = 0; i < $("input.optiontext").size(); i++) {
				for (var j = i + 1; j < $("input.optiontext").size(); j++) {
					if ($("#option_" + i + "_optiontext").val() == $("#option_" + j + "_optiontext").val()) {
						$.scrollTo($("#option_" + i + "_optiontext, #option_" + j + "_optiontext").addClass("warning"), scrollduration, scrolloptions);
						ok = confirm("Options " + (i + 1) + " and " + (j + 1) + " are the same -- click OK to continue regardless or cancel to edit them");
						if (ok)
							$("#option_" + i + "_optiontext, #option_" + j + "_optiontext").removeClass("error warning");
						else
							break;
					}
				}
				if (!ok) break;
			}
			if (!ok) return false;

			// warn about any identical questions
			for (var i = 0; i < $("textarea.prompt").size(); i++) {
				for (var j = i + 1; j < $("textarea.prompt").size(); j++) {
					if ($("#question_" + i + "_prompt").val() == $("#question_" + j + "_prompt").val()) {
						$.scrollTo($("#question_" + i + "_prompt, #question_" + j + "_prompt").addClass("warning"), scrollduration, scrolloptions);
						ok = confirm("The prompts for questions " + (i + 1) + " and " + (j + 1) + " are the same -- click OK to continue regardless or cancel to edit them");
						if (ok)
							$("#question_" + i + "_prompt, #question_" + j + "_prompt").removeClass("error warning");
						else
							break;
					}
				}
				if (!ok) break;
			}
			if (!ok) return false;

			// confirm the user wanted only one option
			if ($("input.optiontext").size() == 1 && !confirm("There is only one option -- click OK to continue regardless or cancel to add more"))
				return false;

			// confirm the user wanted only one question
			if ($("textarea.prompt").size() == 1 && !confirm("There is only one question -- click OK to continue regardless or cancel to add more"))
				return false;

			return true;
		};

		$(document).ready(function() {
			$("#addoption").click(addoption);
			$(".removeoption").click(removeoption);
			$("#addquestion").click(addquestion);
			$(".removequestion").click(removequestion);
		});
	