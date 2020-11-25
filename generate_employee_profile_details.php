<?php
/*************************************
|
|	Developed By	: Md. Nuruzzaman
|	Date			: 03.06.2014
|
**************************************/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include("file://///192.168.11.252/logic_hrm/includes/common.php");
include('file://///192.168.11.252/logic_hrm/includes/array_function.php');

extract($_REQUEST);

	//Group details
$sql = "SELECT * FROM lib_group WHERE is_deleted = 0 and status_active=1 ORDER BY group_name  ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$group_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$group_details[$row['id']] = $row['group_name'];
}

	//Company details
$sql = "SELECT * FROM lib_company WHERE is_deleted = 0 and status_active=1 ORDER BY company_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$company_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$company_details[$row['id']] = $row['company_name'];
}

	//Location
$sql = "SELECT * FROM lib_location WHERE is_deleted = 0 and status_active=1 ORDER BY location_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$location_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$location_details[$row['id']] =$row['location_name'];
}

	//Diviion details
$sql = "SELECT * FROM lib_division WHERE is_deleted = 0 and status_active=1 ORDER BY division_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$division_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$division_details[$row['id']] = $row['division_name'];
}

	//Department details
$sql = "SELECT * FROM lib_department WHERE is_deleted = 0 and status_active=1 ORDER BY department_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$department_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$department_details[$row['id']] = $row['department_name'];
}

	//Section details
$sql = "SELECT * FROM lib_section WHERE is_deleted = 0 and status_active=1 ORDER BY section_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$section_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$section_details[$row['id']] = $row['section_name'];
}
	//Sub Section
$sql = "SELECT * FROM lib_subsection WHERE is_deleted = 0 and status_active=1 ORDER BY subsection_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$subsection_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$subsection_details[$row['id']] = $row['subsection_name'];
}

	//Designation array
$sql = "SELECT * FROM lib_designation WHERE is_deleted = 0 and status_active=1 ORDER BY custom_designation ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$designation_chart = array();
while( $row = mysql_fetch_assoc( $result )) 
{
	$designation_chart[$row['id']] = $row['custom_designation'];
}

	//Division 
$sql = "SELECT * FROM lib_list_division WHERE status_active=1 and is_deleted = 0 ORDER BY division_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$lib_list_division = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$lib_list_division[$row['id']] = $row['division_name'];
}
	//District
$sql = "SELECT * FROM lib_list_district WHERE status_active=1 and is_deleted = 0 ORDER BY district_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$lib_list_district = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$lib_list_district[$row['id']] = $row['district_name'];
}

	//training
$sql = "SELECT * FROM lib_training WHERE status_active=1 and is_deleted = 0 ORDER BY training_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$training_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$training_details[$row['id']] = $row['training_name'];
}

function my_old($dob)
{
	$now = date('d-m-Y');
	$dob = explode('-', $dob);
	$now = explode('-', $now);
	$mnt = array(1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	
	if (($now[2]%400 == 0) or ($now[2]%4==0 and $now[2]%100!=0)) $mnt[2]=29;
	
	if($now[0] < $dob[0])
	{
		$now[0] += $mnt[$now[1]-1];
		$now[1]--;
	}
	if($now[1] < $dob[1])
	{
		$now[1] += 12;
		$now[2]--;
	}
	if($now[2] < $dob[2]) return false;
	return  array('year' => $now[2] - $dob[2], 'mnt' => $now[1] - $dob[1], 'day' => $now[0] - $dob[0]);
}

// start employee_profile_details		
if($action=="action_employee_profile_details")
{
	//echo "emp== ".$emp_code." id== ".$id_card; die;
	if($id_card=="") $where_con="emp_code='$emp_code'"; else $where_con="id_card_no='$id_card'";
	if($emp_code=="") $where_con="id_card_no='$id_card'"; else $where_con="emp_code ='$emp_code'";
	if($id_card!="" && $emp_code!="") $where_con="id_card_no='$id_card' and mp_code ='$emp_code'";
	
	$sql="select *, CONCAT(first_name, ' ', middle_name, ' ', last_name) AS  name from hrm_employee where $where_con";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result);
	
	$sql_company= "SELECT group_id FROM lib_company WHERE id=$row[company_id] ORDER BY company_name ASC";
	$result_company= mysql_query( $sql_company );
	$row_company= mysql_fetch_array( $result_company );
	
	$sql_present_address="select * from hrm_employee_address where emp_code='$row[emp_code]' and address_type=0";
	$result_present_address=mysql_query($sql_present_address);
	$row_present_address=mysql_fetch_array($result_present_address);

	$sql_permanent_address="select * from hrm_employee_address where emp_code='$row[emp_code]' and address_type=1";
	$result_permanent_address=mysql_query($sql_permanent_address);
	$row_permanent_address=mysql_fetch_array($result_permanent_address);

	$sql_alternate_address="select * from hrm_employee_address where emp_code='$row[emp_code]' and address_type=2";
	$result_alternate_address=mysql_query($sql_alternate_address);
	$row_alternate_address=mysql_fetch_array($result_alternate_address);

	$sql_employee_family="select * from hrm_employee_family where emp_code='$row[emp_code]'";
	$result_employee_family=mysql_query($sql_employee_family);
	//$row_employee_family=mysql_fetch_array($result_employee_family);
	foreach ($emp_relation as $r_id=> $relation){
		$emp_relation[$r_id];
	}

	$sql_photo="select location from resource_photo where identifier='$row[emp_code]' and type=0";
	$result_photo=mysql_query($sql_photo);
	$row_photo=mysql_fetch_array($result_photo);
	
	$sql_education="select * from hrm_employee_education where emp_code='$row[emp_code]' and education_nature=0 order by passing_year DESC";
	$result_education=mysql_query($sql_education);
	$row_education=mysql_fetch_array($result_education);
	
	$sql_professional="select * from hrm_employee_education where emp_code='$row[emp_code]' and education_nature=1 order by passing_year DESC";
	$result_professional=mysql_query($sql_professional);
	//$row_professional=mysql_fetch_array($result_professional);
	
	$sql_training="select * from hrm_training_dtls where emp_code='$row[emp_code]' order by id DESC";
	$result_training=mysql_query($sql_training);
	//$row_training=mysql_fetch_array($result_training);
	$training_id="";
	while($row_training=mysql_fetch_array($result_training))
	{
		$training_id.=$row_training[id_mst].",";
	}
	$training=substr($training_id,0,-1);
	
	$sql_training_details="select * from hrm_training_mst where id in ($training) order by training_date DESC";
	$result_training_details=mysql_query($sql_training_details);
	//$row_training_details=mysql_fetch_array($_details);
	
	$sql_experience="select * from hrm_employee_experience where emp_code='$row[emp_code]' order by id DESC";
	$result_experience=mysql_query($sql_experience);
	//$row_experience=mysql_fetch_array($result_experience);
	
	$sql_promotion="select * from hrm_employee_promotion where emp_code='$row[emp_code]' order by id DESC";
	$result_promotion=mysql_query($sql_promotion);
	//$row_promotion=mysql_fetch_array($result_promotion);
	
	$sql_increment="select * from hrm_increment_mst where emp_code='$row[emp_code]' and is_approved=2 order by effective_date DESC";
	$result_increment=mysql_query($sql_increment);
	//$row_increment=mysql_fetch_array($result_increment);
	
	$sql="Select * from hrm_employee_transfer where emp_code=$emp_code and status_active=1 and is_deleted=0 order by id DESC";
	$result=mysql_query($sql);

	$sql_leave_bl="select * from hrm_leave_balance where emp_code='$row[emp_code]'";
	$result_leave_bl=mysql_query($sql_leave_bl);
	$leave_lm_cl=0;$leave_la_cl=0;$leave_bl_cl=0;
	$leave_lm_sl=0;$leave_la_sl=0;$leave_bl_sl=0;
	$leave_lm_lwp=0;$leave_la_lwp=0;$leave_bl_lwp=0;
	$leave_lm_rl=0;$leave_la_rl=0;$leave_bl_rl=0;
	$leave_lm_spl=0;$leave_la_spl=0;$leave_bl_spl=0;
	$leave_lm_el=0;$leave_la_el=0;$leave_bl_el=0;
	$leave_lm_edl=0;$leave_la_edl=0;$leave_bl_edl=0;
	
	while($row_leave_bl=mysql_fetch_assoc($result_leave_bl))
	{
		if($row_leave_bl[leave_type]=="CL") 
		{
			$leave_lm_cl=$row_leave_bl[leave_limit];
			$leave_la_cl=$row_leave_bl[leave_availed];
			$leave_bl_cl=$row_leave_bl[balance];
		}
		if($row_leave_bl[leave_type]=="SL") 
		{
			$leave_lm_sl=$row_leave_bl[leave_limit];
			$leave_la_sl=$row_leave_bl[leave_availed];
			$leave_bl_sl=$row_leave_bl[balance];
		}
		if($row_leave_bl[leave_type]=="LWP") 
		{
			$leave_lm_lwp=$row_leave_bl[leave_limit];
			$leave_la_lwp=$row_leave_bl[leave_availed];
			$leave_bl_lwp=$row_leave_bl[balance];
		}
		if($row_leave_bl[leave_type]=="RL") 
		{
			$leave_lm_rl=$row_leave_bl[leave_limit];
			$leave_la_rl=$row_leave_bl[leave_availed];
			$leave_bl_rl=$row_leave_bl[balance];
		}
		if($row_leave_bl[leave_type]=="SpL") 
		{
			$leave_lm_spl=$row_leave_bl[leave_limit];
			$leave_la_spl=$row_leave_bl[leave_availed];
			$leave_bl_spl=$row_leave_bl[balance];
		}
		if($row_leave_bl[leave_type]=="EL") 
		{
			$leave_lm_el=$row_leave_bl[leave_limit];
			$leave_la_el=$row_leave_bl[leave_availed];
			$leave_bl_el=$row_leave_bl[balance];
		}
		if($row_leave_bl[leave_type]=="EdL") 
		{
			$leave_lm_edl=$row_leave_bl[leave_limit];
			$leave_la_edl=$row_leave_bl[leave_availed];
			$leave_bl_edl=$row_leave_bl[balance];
		}
	}
	
	$sql_resource="select * from hrm_resource_issue_item_details where emp_code='$row[emp_code]'order by id DESC";
	$result_resource=mysql_query($sql_resource);
	//$row_resource=mysql_fetch_array($result_resource);
	//echo $sql_education;die;
	?>
	<div style="width:970px;" align="left">
		<div align="left" style="padding-bottom:5px;" id="print">
			<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>
			<input type="button" onclick="print_report()" value="Print" class="formbutton"  style=" width:80px;"/>
		</div>
		<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all" id="tbl_personal_info">
			<!--<thead><th align='center' colspan="7">Personal Information</th></thead>-->
			<tbody>
				<tr height="35px" valign="middle">
					<td align='center' colspan="7" style="font-size:18px; font-weight:bold;"><?php echo $group_details[$row_company['group_id']];?></td>
				</tr>
				<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="7" style="font-size:14px;"><b>PERSONAL INFORMATION</b></td></tr> 
				<tr height="25px" valign="middle">
					<td width="140px"><b>Name</b></td>
					<td colspan="3"><?php echo $row['name'];?></td>
					<td width="110px"><b>Designation</b></td>
					<td width="150px"><?php echo $designation_chart[$row['designation_id']];?></td>
					<td rowspan="8" valign="middle" align="center"><img src='../../../<?php echo $row_photo[location]?>' width="130px" height="149"/></td>
				</tr>
				<tr height="25px" valign="middle">
					<td width="140px"><b>ID Card No</b></td>
					<td width="150px"><?php echo $row[id_card_no];?></td>
					<td width="90px"><b>Joining Date</b></td>
					<td width="150px"><?php list($y,$m,$d)=explode("-",$row['joining_date']); echo $d."-".$m."-".$y;?></td>
					<td width="110px"><b>Service Lenth</b></td>
					<td width="180px">
						<?php
						$joining_date=convert_to_mysql_date($row['joining_date']);
						$service_length = my_old($joining_date);
						printf("%d years, %d months, %d days\n", $service_length[year], $service_length[mnt], $service_length[day]);
						?>
					</td>
				</tr>

				<tr height="25px" valign="middle">
					<td width="140px"><b>Father's Name</b></td>
					<td width="150px"><?php echo $row[father_name];?></td>
					<td width="90px"><b>Mother's Name</b></td>
					<td width="150px"><?php echo $row[mother_name];?></td>
					<td width="110px"><b>Spous Name</b></td>
					<td width="180px"><?php echo $row[spouse_name];?></td>
				</tr>

				<tr height="25px" valign="middle">
					<td width="140px"><b>Company/Unit</b></td>
					<td width="150px"><?php echo $company_details[$row[company_id]];?></td>
					<td width="90px"><b>Department</b></td>
					<td width="150px"><?php echo $department_details[$row[department_id]];?></td>
					<td width="110px"><b>National ID No</b></td>
					<td width="180px"><?php echo $row[national_id];?></td>
				</tr>
				<tr height="25px" valign="middle">
					<td width="140px"><b>Date of Birth</b></td>
					<td width="150px"><?php list($y,$m,$d)=explode("-",$row['dob']); echo $d."-".$m."-".$y;?></td>
					<td width="90px"><b>Age</b></td>
					<td width="150px"><?php $days_age =datediff("yyyy",$row["dob"],date("d-m-Y"));echo $days_age." years";?></td>
					<td width="110px"><b>Marital Status</b></td>
					<td width="180px"><?php echo $marital_status_arr[$row["marital_status"]];?></td>
				</tr>
				<tr height="25px" valign="middle">
					<td width="140px"><b>Religion</b></td>
					<td width="150px"><?php echo $row[religion];?></td>
					<td width="90px"><b>Gender</b></td>
					<td width="150px"><?php echo $sex_arr[$row[sex]];?></td>
					<td width="110px"><b>Blood Group</b></td>
					<td width="180px"><?php echo $blood_group[$row[blood_group]]?></td>
				</tr>
				<tr height="25px" valign="middle">
					<td width="140px"><b>Nationality</b></td>
					<td width="150px"><?php echo $row[nationality];?></td>
					<td width="90px"><b>Mobile No</b></td>
					<td width="150px"><?php echo $row_present_address[mobile_no];?></td>
					<td width="110px"><b>Current Gross</b></td>
					<td width="180px"><?php echo $row[gross_salary]?></td>
				</tr>
				<tr height="25px" valign="middle">
					<td width="140px"><b>Present Address</b></td>
					<td colspan="5">
						<?php 
						if($row_present_address[house_no]=="") $house_no=""; else $house_no="House No # ".$row_present_address[house_no].", ";
						if($row_present_address[road_no]=="") $road_no=""; else $road_no="Road No # ".$row_present_address[road_no].", ";
						if($row_present_address[post_code]=="") $post_code=""; else $post_code="P.O - ".$row_present_address[post_code].", ";
						if($row_present_address[thana]=="") $thana=""; else $thana="P.S - ".$row_present_address[thana].", ";
						if($row_present_address[district_id]=="") $district=""; else $district="Dist - ".$lib_list_district[$row_present_address[district_id]];
						echo $house_no.$road_no.$post_code.$thana.$district;
						?>
					</td>
				</tr>
				<tr height="25px" valign="middle">
					<td width="140px"><b>Permanent Address</b></td>
					<td colspan="5">
						<?php 
						if($row_permanent_address[village]=="") $village=""; else $village="Vill - ".$row_permanent_address[village].", ";
						if($row_permanent_address[post_code]=="") $post_code=""; else $post_code="P.O - ".$row_permanent_address[post_code].", ";
						if($row_permanent_address[thana]=="") $thana=""; else $thana="P.S - ".$row_permanent_address[thana].", ";
						if($row_permanent_address[district_id]=="") $district=""; else $district="Dist - ".$lib_list_district[$row_permanent_address[district_id]];
						echo $village.$post_code.$thana.$district;
						?>
					</td>
				</tr>
				<tr height="25px" valign="middle">
					<td width="140px"><b>Alternate Address</b></td>
					<td colspan="5">
						<?php 
						if($row_alternate_address[village]=="") $village=""; else $village="Vill - ".$row_alternate_address[village].", ";
						if($row_alternate_address[post_code]=="") $post_code=""; else $post_code="P.O - ".$row_alternate_address[post_code].", ";
						if($row_alternate_address[thana]=="") $thana=""; else $thana="P.S - ".$row_alternate_address[thana].", ";
						if($row_alternate_address[district_id]=="") $district=""; else $district="Dist - ".$lib_list_district[$row_alternate_address[district_id]];
						echo $village.$post_code.$thana.$district;
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		if(mysql_num_rows($result_education)!=0)
		{
			?>
			<div style="height:10px;"></div>
			<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
				<!--<thead><th align='center' colspan="6">Educational Information</th></thead>-->
				<tbody>
					<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="6" style="font-size:14px;"><b>ACADEMIC INFORMATION</b></td></tr> 
					<tr height="25px" valign="middle">
						<td width="120px" align="center"><b>Exam Name</b></td>
						<td width="150px" align="center"><b>Discipline</b></td>
						<td width="250px" align="center"><b>Institute Name</b></td>
						<td width="200px" align="center"><b>Board/University</b></td>
						<td width="120px" align="center"><b>Division/CGPA</b></td>
						<td width="120px" align="center"><b>Passing Year</b></td>
					</tr>
					<?php 
					while($row_education=mysql_fetch_array($result_education))
					{
						?>
						<tr height="25px" valign="middle">
							<td align="center"><?php echo $row_education[exam_name];?></td>
							<td align="center"><?php echo $row_education[discipline];?></td>
							<td align="center"><?php echo $row_education[institution];?></td>
							<td align="center"><?php echo $row_education[board ];?></td>
							<td align="center"><?php echo $row_education[result ];?></td>
							<td align="center"><?php echo $row_education[passing_year];?></td>
						</tr>
						<?php					
					}
					?>
				</tbody>
			</table>
			<?php 
		}
		if(mysql_num_rows($result_professional)!=0)
		{
			?>
			<div style="height:10px;"></div>
			<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
				<!--<thead><th align='center' colspan="4">Diploma Course/Professional Course</th></thead>-->
				<tbody> 
					<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="4" style="font-size:14px;"><b>DIPLOMA/PROFESSIONAL COURSE</b></td></tr>
					<tr height="25px" valign="middle">
						<td width="270px" align="center"><b>Name of Degree</b></td>
						<td width="450px" align="center"><b>Institute Name</b></td>
						<td width="120px" align="center"><b>Result</b></td>
						<td width="120px" align="center"><b>Year</b></td>
					</tr>
					<?php 
					while($row_professional=mysql_fetch_array($result_professional))
					{
						?>
						<tr height="25px" valign="middle">
							<td align="center"><?php echo $row_professional[exam_name];?></td>
							<td align="center"><?php echo $row_professional[institution];?></td>
							<td align="center"><?php echo $row_professional[result ];?></td>
							<td align="center"><?php echo $row_professional[passing_year];?></td>
						</tr>
						<?php					
					}
					?>
				</tbody>
			</table>
			<?php 
		}if(mysql_num_rows($result_employee_family)!=0)
		{ 
			?>
			<div style="height:10px;"></div>
			<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
				<!--<thead><th align='center' colspan="5">Training Record</th></thead>-->
				<tbody> 
					<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="6" style="font-size:14px;"><b>FAMILY INFORMATION</b></td></tr>
					<tr height="25px" valign="middle">
						<td width="270px" align="center"><b>Name</b></td>
						<td width="125px" align="center"><b>Relation</b></td>
						<td width="125px" align="center"><b>Date of Birth</b></td>
						<td width="70px" align="center"><b>Age </b></td>
						<td width="250" align="center"><b>Occupation </b></td>
						<td width="120px" align="center"><b>Contact No</b></td>
					</tr>
					<?php 
					while($row_employee_family=mysql_fetch_array($result_employee_family))
					{ 
						?>
						<tr height="25px" valign="middle">
							<td align="center"><?php echo $row_employee_family[name];?></td>
							<td align="center"><?php  echo $emp_relation[$row_employee_family[relation]]; ?></td>
							<td align="center"><?php list($y,$m,$d)=explode("-",$row_employee_family['dob']); echo $d."-".$m."-".$y;?></td>
							<td align="center"><?php $days_age =datediff("yyyy",$row_employee_family["dob"],date("d-m-Y"));echo $days_age." years";?></td>
							<td align="center"><?php echo $row_employee_family[occupation];?></td>
							<td align="center"><?php echo $row_employee_family[contact_no];?></td>
						</tr>
						<?php					
					}
					?>
				</tbody>
			</table>
			<?php 
		}
		if(mysql_num_rows($result_training)!=0)
		{
			?>
			<div style="height:10px;"></div>
			<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
				<!--<thead><th align='center' colspan="5">Training Record</th></thead>-->
				<tbody> 
					<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="5" style="font-size:14px;"><b>TRAINING RECORD</b></td></tr>
					<tr height="25px" valign="middle">
						<td width="270px" align="center"><b>Training Name</b></td>
						<td width="125px" align="center"><b>Start Date</b></td>
						<td width="125px" align="center"><b>End Date</b></td>
						<td width="320px" align="center"><b>Provided By</b></td>
						<td width="120px" align="center"><b>Duration days</b></td>
					</tr>
					<?php 
					while($row_training_details=mysql_fetch_array($result_training_details))
					{
						?>
						<tr height="25px" valign="middle">
							<td align="center"><?php echo $training_details[$row_training_details[training_id]];?></td>
							<td align="center"><?php list($y,$m,$d)=explode("-",$row_training_details['training_date']); echo $d."-".$m."-".$y;?></td>
							<td align="center">
								<?php 
								list($y,$m,$d)=explode("-",date('Y-m-d', strtotime("+$row_training_details[duration] days $row_training_details[training_date]")));
								echo $d."-".$m."-".$y;
								?>
							</td>
							<td align="center"><?php echo $row_training_details[provided_by ];?></td>
							<td align="center"><?php echo $row_training_details[duration];?></td>
						</tr>
						<?php					
					}
					?>
				</tbody>
			</table>
			<?php 
		}
		if(mysql_num_rows($result_experience)!=0)
		{
			?>
			<div style="height:10px;"></div>
			<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
				<!--<thead><th align='center' colspan="6">Previous Employment/Experience</th></thead>-->
				<tbody> 
					<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="6" style="font-size:14px;"><b>PREVIOUS EMPLOYMENT/EXPERIENCE</b></td></tr>
					<tr>
						<td width="270px" align="center"><b>Organization Name</b></td>
						<td width="180px" align="center"><b>Designation</b></td>
						<td width="110px" align="center"><b>Joining Date</b></td>
						<td width="110px" align="center"><b>Resigning Date</b></td>
						<td width="170px" align="center"><b>Service Length</b></td>
						<td width="120px" align="center"><b>Gross Salary</b></td>
					</tr>
					<?php 
					while($row_experience=mysql_fetch_array($result_experience))
					{
						?>
						<tr height="25px" valign="middle">
							<td align="center"><?php echo $row_experience[organization_name];?></td>
							<td align="center"><?php echo $row_experience[designation];?></td>
							<td align="center"><?php list($y,$m,$d)=explode("-",$row_experience['joining_date']); echo $d."-".$m."-".$y;?></td>
							<td align="center"><?php list($y,$m,$d)=explode("-",$row_experience['resigning_date']); echo $d."-".$m."-".$y;?></td>
							<td align="center"><?php echo $row_experience[sevice_length];?></td>
							<td align="center"><?php echo $row_experience[gross_salary];?></td>
						</tr>
						<?php					
					}
					?>
				</tbody>
			</table>
			<?php 
		}
		if(mysql_num_rows($result_promotion)!=0)
		{
			?>
			<div style="height:10px;"></div>
			<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
				<!--<thead><th align='center' colspan="5">Progress History/Promotion</th></thead>-->
				<tbody> 
					<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="6" style="font-size:14px;"><b>PROGRESS HISTORY/PROMOTION</b></td></tr>
					<tr height="25px" valign="middle">
						<td width="120px" align="center"><b>Effective Date</b></td>
						<td width="150px" align="center"><b>Position</b></td>
						<td width="200px" align="center"><b>Reason</b></td>
						<td width="120px" align="center"><b>Salary Grade</b></td>
						<td width="240px" align="center"><b>Organizational Unit</b></td>
						<td width="140px" align="center"><b>APR Score</b></td>
					</tr>
					<?php 
					while($row_promotion=mysql_fetch_array($result_promotion))
					{
						?>
						<tr height="25px" valign="middle">
							<td align="center"><?php list($y,$m,$d)=explode("-",$row_promotion['effective_date']); echo $d."-".$m."-".$y;?></td>
							<td align="center"><?php echo $designation_chart[$row_promotion[new_designation]];?></td>
							<td align="center"><?php echo "Promotion";?></td>
							<td align="center">
								<?php 
								$salary_grade=return_field_value("salary_grade","lib_designation","id='$row_promotion[new_designation]' and status_active=1 and is_deleted=0");
								echo $salary_grade;
								?>
							</td>
							<td align="center"><?php echo $company_details[$row_promotion[company_id ]];?></td>
							<td align="center"><?php echo $row_promotion[apr_score ];?></td>
						</tr>
						<?php					
					}
					?>
				</tbody>
			</table>
			<?php 
		}
		?>
		<div style="height:10px;"></div>
		<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
			<!--<thead><th align='center' colspan="6">Increment History</th></thead>-->
			<tbody> 
				<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="6" style="font-size:14px;"><b>INCREMENT HISTORY</b></td></tr>
				<tr height="25px" valign="middle">
					<td width="120px" align="center"><b>Effective Date</b></td>
					<td width="150px" align="center"><b>Previous Gross</b></td>
					<td width="165px" align="center"><b>Increment Amount</b></td>
					<td width="165px" align="center"><b>New Gross</b></td>
					<td width="120px" align="center"><b>Salary Grade</b></td>
					<td width="240px" align="center"><b>Reason</b></td>
				</tr>
				<?php
				$dtt="";
				if(mysql_num_rows($result_increment)!=0)
				{
						//$date = '2010-09-16';
						//echo date('Y-m-d', strtotime("+12 months $date"));
					while($row_increment=mysql_fetch_array($result_increment))
					{
						$dt.=$row_increment['effective_date'].",";
						?>
						<tr height="25px" valign="middle">
							<td align="center"><?php list($y,$m,$d)=explode("-",$row_increment['effective_date']); echo $d."-".$m."-".$y;?></td>
							<td align="center"><?php echo $row_increment[old_gross_salary];?></td>
							<td align="center"><?php echo $row_increment[increment_amount];?></td>
							<td align="center"><?php echo $row_increment[new_gross_salary];?></td>
							<td align="center"><?php echo $row_increment[new_salary_grade];?></td>
							<td align="center"><?php echo $increment_reason_arr[$row_increment[reason]];?></td>
						</tr>
						<?php					
					}
					$dts=explode(",",$dt);
					$dtt.=$dts[0];
				}
				else
				{
					$dtt.=$row[joining_date];
				}
				?>
				<tr>
					<td align='left' colspan="6" style="font-size:13px;"><b>Increment Due Date:</b>
						<?php list($y,$m,$d)=explode("-",date('Y-m-d', strtotime("+12 months $dtt"))); echo $d."-".$m."-".$y;?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php 
		if(mysql_num_rows($result_leave_bl)!=0)
		{
			?>
			<div style="height:10px;"></div>
			<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
				<!--<thead><th align='center' colspan="8">Leave History</th></thead>-->
				<tbody> 
					<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="8" style="font-size:14px;"><b>LEAVE HISTORY</b></td></tr>
					<tr height="25px" valign="middle">
						<td width="270px" align="center"><b>Particulars</b></td>
						<td width="100px" align="center"><b>CL</b></td>
						<td width="100px" align="center"><b>SL</b></td>
						<td width="100px" align="center"><b>LWP</b></td>
						<td width="100px" align="center"><b>RL</b></td>
						<td width="100px" align="center"><b>SPL</b></td>
						<td width="100px" align="center"><b>EL</b></td>
						<td width="100px" align="center"><b>EDL</b></td>
					</tr>
					<tr height="25px" valign="middle">
						<td align="center"><b>Leave Entitlement</b></td>
						<td align="center"><?php echo $leave_lm_cl;?></td>
						<td align="center"><?php echo $leave_lm_sl;?></td>
						<td align="center"><?php echo $leave_lm_lwp;?></td>
						<td align="center"><?php echo $leave_lm_rl;?></td>
						<td align="center"><?php echo $leave_lm_spl;?></td>
						<td align="center"><?php echo $leave_lm_el;?></td>
						<td align="center"><?php echo $leave_lm_edl;?></td>
					</tr>
					<tr height="25px" valign="middle">
						<td align="center"><b>Leave Taken/Enjoyed</b></td>
						<td align="center"><?php echo $leave_la_cl;?></td>
						<td align="center"><?php echo $leave_la_sl;?></td>
						<td align="center"><?php echo $leave_la_lwp;?></td>
						<td align="center"><?php echo $leave_la_rl;?></td>
						<td align="center"><?php echo $leave_la_spl;?></td>
						<td align="center"><?php echo $leave_la_el;?></td>
						<td align="center"><?php echo $leave_la_edl;?></td>
					</tr>
					<tr height="25px" valign="middle">
						<td align="center"><b>Balance of Leave</b></td>
						<td align="center"><?php echo $leave_bl_cl;?></td>
						<td align="center"><?php echo $leave_bl_sl;?></td>
						<td align="center"><?php echo $leave_bl_lwp;?></td>
						<td align="center"><?php echo $leave_bl_rl;?></td>
						<td align="center"><?php echo $leave_bl_spl;?></td>
						<td align="center"><?php echo $leave_bl_el;?></td>
						<td align="center"><?php echo $leave_bl_edl;?></td>
					</tr>
				</tbody>
			</table>
			<?php 
		}
		if(mysql_num_rows($result_resource)!=0)
		{
			?>
			<div style="height:10px;"></div>
			<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
				<!--<thead><th align='center' colspan="5">Resource Status</th></thead>-->
				<tbody> 
					<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="5" style="font-size:14px;"><b>RESOURCE STATUS</b></td></tr>
					<tr height="25px" valign="middle">
						<td width="140px" align="center"><b>Resource Category</b></td>
						<td width="400px" align="center"><b>Item Description</b></td>
						<td width="120px" align="center"><b>Item Quantity</b></td>
						<td width="150px" align="center"><b>Rate</b></td>
						<td width="150px" align="center"><b>Amount</b></td>
					</tr>
					<?php 
					while($row_resource=mysql_fetch_array($result_resource))
					{
						?>
						<tr height="25px" valign="middle">
							<td align="center"><?php echo $resource_alo_category[$row_resource[resource_category]];?></td>
							<td align="center"><?php echo $row_resource[issue_item_name];?></td>
							<td align="center"><?php echo $row_resource[issue_item_quantity];?></td>
							<td align="center"><?php echo $row_resource[issue_item_rate];?></td>
							<td align="center"><?php echo $row_resource[amount];?></td>
						</tr>
						<?php					
					}
					?>
				</tbody>
			</table> 
			<?php 
		}
		if(mysql_num_rows($result)!=0)
		{
			?> <div style="height:10px;"></div>
			<table border="1" cellspacing="0" cellpadding="0" width="970px" class="rpt_table" rules="all">
				<!--<thead><th align='center' colspan="5">Resource Status</th></thead>-->
				<tbody> 
					<tr bgcolor="#A6CAF0" height="25px" valign="middle"><td align='center' colspan="7" style="font-size:14px;"><b>TRANSFER INFORMATION</b></td></tr>

					<td width="170px" align='center'> <b>Company </b></td>
					<td width="200px" align='center'> <b>Location </b></td>
					<td width="90px" align='center'> <b>Division </b></td>
					<td width="120px" align='center'> <b>Department </b></td>
					<td width="100px" align='center'> <b>Section </b></td>
					<td width="120px" align='center'> <b>Subsection </b></td>
					<td width="100px" align='center'> <b>Effictive Date </b></td>


					<?php
					$sl=0; 
					while($row=mysql_fetch_array($result))
					{

						list($y,$m,$d)=explode("-",$row[effective_date]);	
						?>
						<tr  style="cursor:pointer;">
							<td align="center"><b><?php echo $company_details[$row[company_id]]; ?></b></td>
							<td align="center"><?php echo $location_details[$row[location_id]]; ?></td>
							<td align="center"><?php echo $division_details[$row[division_id]]; ?></td>
							<td align="center"><?php echo $department_details[$row[department_id]]; ?></td>
							<td align="center"><?php echo $section_details[$row[section_id]]; ?></td>
							<td align="center"><?php echo $subsection_details[$row[subsection_id]]; ?></td>
							<td align="center"><?php echo $d."-".$m."-".$y ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php
		}
		?>
	</div>
	<?	
	$html = ob_get_contents();
	ob_clean();
	
	foreach (glob(""."*.pdf") as $filename) {			
		@unlink($filename);
	}

	echo "$html####$filename";
	exit();
}// end employee_profile_details

function add_month($orgDate,$mon)
{
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}	

function return_field_value($fdata,$tdata,$cdata)
{
	$sql_data="select $fdata from  $tdata where $cdata";
	$sql_data_exe=mysql_query($sql_data);
	$sql_data_rslt=mysql_fetch_array($sql_data_exe);
	$m_data  = $sql_data_rslt[0];
	return $m_data;
}
?>