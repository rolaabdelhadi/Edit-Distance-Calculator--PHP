<!DOCTYPE html>
<html lang="en">
  <head>
     <meta charset="UTF-8">
	 <title>Edit Distance Calculator</title>
	 <!-- Bootstrap -->
	 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	 <!-- CSS -->
	 <link rel="stylesheet" href="style.css">
  </head>
  <body>
	 <main>
	   <div class="container">
		 <h1>Edit Distance Calculator</h1>
		 <p class="intro-parag">Please enter the strings you'd like to compare, the calculation method, and press the (Calculate Edit Distance) button.</p>
		 <p class="intro-parag">Notice the edit distance result at the end of the page!</p>
		 <!-- HTML Form -->
		 <form method="post" action="#">
		   <div class="row">
			   <div class= "col-sm">
			   <label for="a">First String:</label>
			   <input type="text" name="first-str" id="a" value="<?php echo isset($_POST["first-str"])? $_POST["first-str"]: ""; ?>">
			   <!-- value set in order to keep field values on screen after submission -->
			   </div>
			</div>
			<br>
			<div class="row">
			   <div class= "col-lg">
			   <label for="b">Second String:</label>
			   <input type="text" name="second-str" id="b" value="<?php echo isset($_POST["second-str"])? $_POST["second-str"]: ""; ?>">
			   </div>
			</div>
			<br>
			<div class="row">
			   <div class= "col-lg">
				   <label>Calculation Method:</label>
				</div>
				<div class= "col-lg">
						<input type="radio" name="calc-meth" id="hamming" value="hamming"
							<?php if (isset($_POST["calc-meth"]) && $_POST["calc-meth"] == "hamming") {echo "checked";} ?> required > <!-- to keep field values on screen after submission -->
						<label for="hamming" class="rad-but">Hamming</label>
				</div>
				<div class= "col-lg">
						<input type="radio" name="calc-meth" id="levenshtein" value="levenshtein"
							<?php if (isset($_POST["calc-meth"]) && $_POST["calc-meth"] == "levenshtein") {echo "checked";} ?>>
						<label for="levenshtein" class="rad-but">Levenshtein</label>
			   </div>
			</div>
			<br>
			   <input type="submit" name="submit" value="Calculate Edit Distance" class="btn btn-primary submit">
			<br>
		 </form>

         <!-- PHP Code -->
		 <?php 
			 if (isset($_POST["submit"])) // Form is submitted
			 {				 
				 // Get input fields after form submission
				 $a          = $_POST["first-str"];  // First String
				 $b          = $_POST["second-str"]; // Second String
				 $calc_meth  = $_POST["calc-meth"];  // Calculation Method Radio
                 
				 /***** Classes ******/
				 class hamming_class {
					// Properties
					private $first_str;
					private $second_str;
					private $calc_meth;
					
					// Methods
					public function set_props($str1, $str2, $calc_meth){
						$this->first_str  = $str1;
						$this->second_str = $str2;
						$this->calc_meth  = $calc_meth;
					}
					
					public function hamming_dis($a, $b)
					{
					   $dist = 0; // Initializing the distance
                       
					   // Handle strings with different lengths
					   if (strlen($a) > strlen($b))
					   {
						   $len_diff = strlen($a) - strlen($b);
						   $dist = $len_diff;

						   $a_new = substr($a, 0, $len_diff * -1); // no need to compare the extra characters
						   $b_new = $b;
					   } elseif (strlen($b) > strlen($a)) {
						   $len_diff = strlen($b) - strlen($a);
						   $dist = $len_diff;

						   $b_new = substr($b, 0, $len_diff * -1); // no need to compare the extra characters
						   $a_new = $a;
					   }
					   // Equal lengths
					   else {
						   $a_new = $a;
						   $b_new = $b;
					   }

					   // Loop and compare strings
					   for ($i = 0; $i < strlen($a_new); $i++){
						   if (substr($a, $i, 1) != substr($b, $i, 1)) {
							   $dist += 1; // already has a value in case of different strings lengths
						   }
					   }
					   return $dist;
					}
				 }
				 
	             class levenshtein_class {
					// Properties
					private $first_str;
					private $second_str;
					private $calc_meth;
					 
					// Methods
					public function set_props($str1, $str2, $calc_meth){
						$this->first_str  = $str1;
						$this->second_str = $str2;
						$this->calc_meth  = $calc_meth;
					}
					 
					public function levenshtein_dis($a, $b, $len_a, $len_b)
					{
						// Base Case
						// First string is empty, insert the second string into the first
						if ($len_a == 0){
							return $len_b;
					    }
						
						// Second string is empty, remove all characters of the first string
						if ($len_b == 0){
							return $len_a;
					    }
						
						// Last character of the two strings are the SAME, change NOTHING.
						// And recall the function for the remaining strings
						if ($a[$len_a - 1] == $b[$len_b - 1])
						{
							return $this->levenshtein_dis($a, $b, $len_a - 1, $len_b - 1);
						}
						 
						// If last characters are NOT the same,
						// try all 3 possible operations on the last character of the first string.
						// Recursively compute MINIMUM ditance for all 3 operations (insert, remove, or subsitute)
						// and choose MINIMUM of 3 values.
					    
						return 1 + min($this->levenshtein_dis($a, $b, $len_a    , $len_b - 1),  // Insert into first string
									   $this->levenshtein_dis($a, $b, $len_a - 1, $len_b),      // Remove from first string
									   $this->levenshtein_dis($a, $b, $len_a - 1, $len_b - 1)); // Subsitute/replace the character in first string 
									                                                            // as the character in the second string
					}
				 }
				 
				 // 1. Hamming Method
				 if ($calc_meth == "hamming")
				 {
                    $hamming_obj = new hamming_class(); // create object
					$hamming_obj->set_props($a, $b, $calc_meth); // call method that sets properties received from frontend
					
					$result = $hamming_obj->hamming_dis($a, $b); // call the computing method
					echo "<p class='result'>The " .$calc_meth ." edit distance = ".$result;"</p>"; // Display final result on web page
				 }

				 // 2. Levenshtein Method (recursive minimum)
				 elseif ($calc_meth == "levenshtein")
				 {
                    $levenshtein_obj = new levenshtein_class(); // create object
					$levenshtein_obj->set_props($a, $b, $calc_meth); //  call method that sets properties received from frontend
					
					$result = $levenshtein_obj->levenshtein_dis($a, $b, strlen($a), strlen($b)); // call the computing method
					echo "<p class='result'>The " .$calc_meth ." edit distance = ".$result;"</p>"; // Display final result on web page
				 }
			 } // Form submitted
		 ?>
	   </div>
	 </main>
  </body>
</html>