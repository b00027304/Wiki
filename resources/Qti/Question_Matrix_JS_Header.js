addquestion = function() {
			// clone the last question on the list and increment its id
			//alert('QM JS');
			var newquestion = $("#questions tr.question:last").clone();
			var oldid = parseInt($("textarea", newquestion).attr("id").split("_")[1]);
			var newid = oldid + 1;

			// give it the new id number and wipe its text
			newquestion.attr("id", "question_" + newid);
			$("textarea", newquestion).attr("id", "question_" + newid + "_prompt").attr("name", "question_" + newid + "_prompt").val("").removeClass("error warning");

			// clear all its checkboxes and update their question numbers
			$("td.answer input", newquestion).removeAttr("checked");
			$("td.answer label.true", newquestion).attr("id", "question_" + newid + "_true");
			$("td.answer label.false", newquestion).attr("id", "question_" + newid + "_false");
			$("td.answer label input", newquestion).attr("name", "question_" + newid + "_answer");

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
				$("td.answer label.true", this).attr("id", "question_" + i + "_true");
				$("td.answer label.false", this).attr("id", "question_" + i + "_false");
				$("td.answer label input", this).attr("name", "question_" + i + "_answer");
				$(this).removeClass("row" + ((i + 1) % 2)).addClass("row" + (i % 2));
				i++;
			});
		};

		edititemsubmitcheck_itemspecificerrors = function() {
			// true or false must be chosen for each question
			var ok = true;
			$("#questions tr.question td.answer").each(function(n) {
				if ($("input:checked", this).size() == 0) {
					$.scrollTo($(this).addClass("error"), scrollduration, scrolloptions);
					alert("No correct response has been chosen for question " + (n + 1));
					ok = false;
					return false;
				}
			});
			if (!ok) return false;

			return true;
		};

		edititemsubmitcheck_itemspecificwarnings = function() {
			// confirm the user wanted any empty boxes
			var ok = true;
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

			// confirm the user wanted only one question
			if ($("textarea.prompt").size() == 1 && !confirm("There is only one question -- click OK to continue regardless or cancel to add more"))
				return false;

			return true;
		};

		$(document).ready(function() {
			$("#addquestion").click(addquestion);
			$(".removequestion").click(removequestion);
		});