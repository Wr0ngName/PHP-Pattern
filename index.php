<?php
	
	/**
	 * By wr0ng.name (http://wr0ng.name) - 2015
	 * 
	 * Simple PHP script to provide an easy GUI to generate a sequence and calculate an offset from it.
	 * Provides same services as pattern_create.rb / pattern_offset.rb but using a fixed set. 
	 * 
	 * Charsets can be changed
	 * 
	 **/

	header('Content-type: text/html; charset=ISO-8859-1');

	// Charset lists
	$setA 	= str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
	$setB 	= str_split('abcdefghijklmnopqrstuvwxyz');
	$setC 	= str_split('0123456789');

	// Max length (here alphabet is (setA*setB*setC): we have 6760 different combinations,
	// so we multiply by 3 for the final amount of chars in the string, here 20280)
	$maxLength = (count($setA) * count($setB) * count($setC)) * 3;

	$alertLength = false;

	// Set length
	$length = (isset($_GET['length']) && intval($_GET['length'])>0)?intval($_GET['length']):500;

	// To run a search, we store the original length, and we generate a full sequence
	if(!empty($_GET['search']) && strlen($_GET['search'])>2 && strlen($_GET['search'])<=100)
	{
		$oldLength = $length;
		$length = $maxLength;
		$search = htmlentities($_GET['search']);
	}
	else
	{

		// Shows warning if not enough combinations for a unique string
		if($length > $maxLength)
		{
			$alertLength = true;
			$length = $maxLength;
		}
	}

	$sequence = '';
	$total = 0;

	for($i = 0; $i < count($setA) && $total < $length; $i++)
	{
		for($j = 0; $j < count($setB) && $total < $length; $j++)
		{
			for($k = 0; $k < count($setC) && $total < $length; $k++)
			{
				// setA
				$sequence .= $setA[$i];
				$total++;
				if($total >= $length) break;

				// setB
				$sequence .= $setB[$j];
				$total++;
				if($total >= $length) break;

				// setC
				$sequence .= $setC[$k];
				$total++;
				if($total >= $length) break;
			}
		}
	}

	// Once we have the full sequence wee get the index of the substring and we cut the full sequence to the desired size
	if(!empty($search))
	{
		$offset = strpos($sequence, $search);
		$length = $oldLength;
		$sequence = substr($sequence, 0, $length);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Unique Sequence Generator</title>
		<meta charset="ISO-8859-1"> 
		<script type='text/javascript'>

		function exportSequenceToTextfile()
		{
			// Generating necessary variables:
			var sequence = document.getElementById("string").value;
			var sequenceBlob = new Blob([sequence], { type:'text/plain' });

			// Generating the link to download
			var link = document.createElement("a");
			link.download = "sequence.txt";

			if (window.webkitURL != null)
			{
				// Chrome: no need to add link to DOM
				link.href = window.webkitURL.createObjectURL(sequenceBlob);
			}
			else
			{
				// Firefox: we add the link to the DOM before clicking and its destroyer
				link.href = window.URL.createObjectURL(sequenceBlob);
				link.onclick = removeFromDOM;
				link.style.display = 'none';
				document.body.appendChild(link);
			}

			// Finally we click the link
			link.click();
		}

		function removeFromDOM(event)
		{
			document.body.removeChild(event.target);
		}

		</script>
	</head>
	<body style="width:50%; min-width:500px; margin:auto;">
		<h1 style="text-align:center;">Unique Sequence Generator</h1>
		<h2>Introduction</h2>
		<p>Often to test buffers/offsets you need to find a way to fill a string with a logic unique sequence, this here is a simple generator.</p>
		<h2>Generate</h2>
		<form method="GET">
			<label for="length">Length:</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="number" placeholder="length" name="length" value="<?php echo $length; ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" value="OK" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" value="Save as Textfile" onclick="exportSequenceToTextfile();" /><br /><br />
			<label>Sequence (<?php echo $length; ?> chars):</label>
			<?php if($alertLength) echo '<p style="color:red; text-align:center;">Alert, charsets are too small to generate a unique string that length. Max. length: '.$maxLength.'</p>'; ?>
			<textarea id="string" style="display:block; width:60%; min-width:400px; min-height:400px; margin:auto;"><?php echo $sequence; ?></textarea>
		</form>
		<h2>Find</h2>
		<p>Just enter a part of the sequence that you want to know the index in the full sequence. Index given is the size of the buffer, or the value of your offset! (Give at least 3 chars)</p>
		<form method="GET">
			<label for="search">Sequence part:</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="hidden" name="length" value="<?php echo $length; ?>" />
			<input type="text" placeholder="sequence part" name="search" maxlength="100" value="<?php echo (isset($search)?$search:''); ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" value="Get index" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo (isset($offset)?'<span style="color:green;">Substring index: '.$offset.'</span>':''); ?>
		</form>
		<br />
		<div style="text-align:right;">By <a href="http://wr0ng.name">wr0ng.name</a></div>
	</body>
</html>
