<script type="text/javascript">
	function toggleBackground(checkBox)
	{
		var row = checkBox.parentNode.parentNode;
		var cells = row.getElementsByClassName('listRow');

		if (checkBox.checked) 
		{
			for (var i = 1; i < cells.length; i++) 
			{
				cells[i].className = "listRow selected";
			};
		} else {
			for (var i = 1; i < cells.length; i++) 
			{
				cells[i].className = "listRow";
			};
		};
	}
	
	// Run through the checkboxes on load to make sure they have the
	// right highlighting
	$$('.multiCheck').each(function(s){toggleBackground(s)});
</script>

</form>