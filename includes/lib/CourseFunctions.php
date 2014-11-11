<?php
include_once ("../common.php");

if (isset ($_GET["course_id"])) {

	$results = "<div class=\"autocompleter\">";

	$dest = $_GET["source"];
	$course = $_GET["course_id"];
	$semester = $_GET["semester"];

	$course = str_replace("%", "", $course);
	$course = str_replace("_", "", $course);

	$db = new DB();
	if ($course != "") {
		if (is_numeric($course[0])) {
			$course = str_replace("-", "", $course);
			//$qry = "SELECT tblCourse.course_number, tblCourse.course_name, tblSection.section FROM tblCourse, tblSection WHERE tblSection.course_number LIKE '$course%' AND tblSection.course_number = tblCourse.course_number AND tblSection.semester = tblCourse.semester ORDER BY \"tblCourse.course_name\" LIMIT 0,50";
			$db->getRecords("SELECT tblCourse.course_number, tblCourse.course_name, tblSection.section FROM tblCourse, tblSection WHERE tblSection.course_number LIKE '$course%' AND tblSection.course_number = tblCourse.course_number AND tblSection.semester = tblCourse.semester AND tblSection.semester = '$semester' ORDER BY \"tblCourse.course_name\" LIMIT 0,50");
		} else {
			//$qry = "SELECT tblCourse.course_number, tblCourse.course_name, tblSection.section FROM tblCourse, tblSection WHERE tblCourse.course_name LIKE '%$course%' AND tblSection.course_number = tblCourse.course_number AND tblSection.semester = tblCourse.semester ORDER BY \"tblCourse.course_name\" LIMIT 0,50";
			$db->getRecords("SELECT tblCourse.course_number, tblCourse.course_name, tblSection.section FROM tblCourse, tblSection WHERE tblCourse.course_name LIKE '%$course%' AND tblSection.course_number = tblCourse.course_number AND tblSection.semester = tblCourse.semester AND tblSection.semester = '$semester' ORDER BY \"tblCourse.course_name\" LIMIT 0,50");
		}

		//echo $qry;
		
		while ($row = $db->getRow()) {
			$number = $row["course_number"];
			$name = $row["course_name"];
			$section = $row["section"];
			
			$reset = $dest."Results";
			$final = $dest."_result";

			$display = substr($number, 0, 2)."-".substr($number, 2, 3);
			if (is_numeric($section))
				$display .= " Lec $section";				
			else
				$display .= " Sec $section";

			$ident = $number.$section;

			$results .= "<div id=\"$ident\" class=\"course_listing\" onclick=\"document.getElementById('$dest').value='$number $section'; document.getElementById('$reset').innerHTML = ''; validate(document.getElementById('$dest'), 'course');\" onmouseover=\"document.getElementById('$ident').className='hover';\" onmouseout=\"document.getElementById('$ident').className='no_hover'\";>$display : $name</div><br/>";
		}
		$results .= "</div>";

		if ($db->numReturned() > 1 || (strlen($number) != strlen($course))) {
			echo $results;
		}
		
	}

}
?>

