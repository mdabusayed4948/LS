<?php
date_default_timezone_set('Asia/Dhaka');
session_start();

include('../../../../includes/common.php');
include('../../../../includes/array_function.php');
include('../../../../includes/common_functions.php');

function search( $array, $key, $value ) { //searh in a two dimensional array by value provided a key
	$results = array();
	
	if( is_array( $array ) ) {
		if( $array[$key] == $value ) $results[] = $array;
		foreach( $array as $subarray ) $results = array_merge( $results, search( $subarray, $key, $value ) );
	}
	return $results;
}

$e_date = time();
$user_only = $_SESSION["user_name"];
extract( $_GET );

//month generated
if($type=="select_month_generate")
{		
	$sql= "SELECT a.* FROM lib_policy_year_periods a, lib_policy_year b where b.id=a.year_id and a.year_id=$id  and b.type=1 ";//and b.is_locked=0
	$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
	echo "<option value=0>-- Select --</option>";
	while( $row = mysql_fetch_assoc( $result ) ) 
	{
		$start_1 = substr($row["starting_date"],0,3);
		$start_2 = substr($row["starting_date"],-1,1);
		$end_1 = substr($row["ending_date"],0,3);
		$end_2 = substr($row["ending_date"],-2,2);
		echo "<option value='".$row[actual_starting_date]."_".$row[actual_ending_date]."'>".$start_1." ".$start_2." - ".$end_1." ".$end_2."</option>";
	}		
	exit();
}



//emp_basic
$sql = "SELECT *, CONCAT(first_name,' ',middle_name,' ',last_name) as name FROM hrm_employee where status_active=1 and is_deleted=0 ORDER BY emp_code ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );
$emp_basic = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$emp_basic[$row['emp_code']] = array();
	foreach( $row AS $key => $value ) {
		$emp_basic[$row['emp_code']][$key] = mysql_real_escape_string( $value );
	}
}	
 
 
 
 // Shift Details
	$shift_lists = array();
	$sql_shift = "SELECT id,shift_start,shift_end,shift_type,grace_minutes,exit_buffer_minutes,first_break_start,first_break_end,second_break_start,second_break_end,lunch_time_start,lunch_time_end,early_out_start,entry_restriction_start,cross_date FROM lib_policy_shift";
	$result_shift = mysql_query( $sql_shift ) or die( $sql_shift . "<br />" . mysql_error() );
	while( $row_shift = mysql_fetch_array( $result_shift ) ) 
	{
		 $shift_lists[$row_shift[id]]['shift_start']=$row_shift[shift_start];
		$shift_lists[$row_shift[id]]['shift_end']=$row_shift[shift_end];
		$shift_lists[$row_shift[id]]['shift_type']=$row_shift[shift_type];
		$shift_lists[$row_shift[id]]['in_grace_minutes'] =add_time( $row_shift[shift_start], $row_shift[grace_minutes]);// Add In Grace Time with In Time    
		$shift_lists[$row_shift[id]]['exit_buffer_minutes']=$row_shift[exit_buffer_minutes];
		
		
		
		$shift_lists[$row_shift[id]]['tiffin_start']=$row_shift[first_break_start];
		$shift_lists[$row_shift[id]]['tiffin_break_min']=datediff(n,$row_shift[first_break_start],$row_shift[first_break_end]);//first_break_start 	first_break_end
		$shift_lists[$row_shift[id]]['tiffin_ends']=$row_shift[first_break_end];
		
		$shift_lists[$row_shift[id]]['dinner_start']=$row_shift[second_break_start];
		$shift_lists[$row_shift[id]]['dinner_break_min']=datediff(n,$row_shift[second_break_start],$row_shift[second_break_end]);
		$shift_lists[$row_shift[id]]['dinner_ends']=$row_shift[second_break_end];
		
		$shift_lists[$row_shift[id]]['lunch_start']=$row_shift[lunch_time_start];
		$shift_lists[$row_shift[id]]['lunch_break_min']=datediff(n,$row_shift[lunch_time_start],$row_shift[lunch_time_end]); 
		$shift_lists[$row_shift[id]]['lunch_break_ends']=$row_shift[lunch_time_end]; 
		if($row_shift[cross_date]!=1)
			$shift_lists[$row_shift[id]]['total_working_min']=datediff(n,$row_shift[shift_start],$row_shift[shift_end])-datediff(n,$row_shift[lunch_time_start],$row_shift[lunch_time_end]);
		else
		{
			 $tmpsttime=date("Y-m-d",time())." ".$row_emp[shift_start];
			 $tmpendtime=add_date(date("Y-m-d",time()),1)." ".$row_shift[shift_end];
			 $shift_lists[$row_shift[id]]['total_working_min']= datediff( n, $tmpsttime, $tmpendtime )-$shift_lists[$row_shift[id]]['lunch_break_min'];
		}
		$shift_lists[$row_shift[id]]['early_out_start']=$row_shift[early_out_start];
		$shift_lists[$row_shift[id]]['entry_restriction_start']=$row_shift[entry_restriction_start];
		$shift_lists[$row_shift[id]]['cross_date']=$row_shift[cross_date];
		
		$lunch_time_duration[$row['id']]['dur']= datediff(n,$row['lunch_time_start'],$row['lunch_time_end']);
		$lunch_time_duration[$row['id']]['stime']= $row['lunch_time_start'];
		$lunch_time_duration[$row['id']]['etime']= $row['lunch_time_end'];
	}

//company_details
$sql = "SELECT * FROM lib_company WHERE is_deleted = 0 ORDER BY company_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );

$company_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$company_details[$row['id']] = array();
	foreach( $row AS $key => $value ) {
		$company_details[$row['id']][$key] = mysql_real_escape_string( $value );
	}
}

$sql = "SELECT * FROM lib_location WHERE is_deleted = 0 ORDER BY location_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );

$location_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$location_details[$row['id']] = mysql_real_escape_string($row['location_name']);
}

$sql = "SELECT * FROM lib_division WHERE is_deleted = 0 ORDER BY division_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );

$division_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$division_details[$row['id']] = mysql_real_escape_string($row['division_name']);
}

$sql = "SELECT * FROM lib_department WHERE is_deleted = 0 ORDER BY department_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );

$department_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$department_details[$row['id']] = mysql_real_escape_string($row['department_name']);
}

$sql = "SELECT * FROM lib_section WHERE is_deleted = 0 ORDER BY section_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );

$section_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$section_details[$row['id']] = mysql_real_escape_string($row['section_name']);
}

$sql = "SELECT * FROM lib_subsection WHERE is_deleted = 0 ORDER BY subsection_name ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );

$subsection_details = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$subsection_details[$row['id']] = mysql_real_escape_string($row['subsection_name']);
}

	//$sql = "SELECT * FROM lib_designation WHERE is_deleted = 0 ORDER BY custom_designation ASC";
$sql = "SELECT * FROM lib_designation WHERE is_deleted = 0 and status_active=1 order by trim(custom_designation) ASC";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );

$designation_chart = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$designation_chart[$row['id']] = mysql_real_escape_string($row['custom_designation']);
}	


if($action=='weekly_ot_hours')
{
	ob_start();	
	$sheet_id=1;
	if ($cbo_salary_sheet=='') {$emp_status="";} 
	else if($cbo_salary_sheet==0 || $cbo_salary_sheet==2 || $cbo_salary_sheet==10){$emp_status="and b.status_active=1";} 
	else if($cbo_salary_sheet==1){$emp_status="and b.status_active=0";}
	else if($cbo_salary_sheet==4){$emp_status="and b.status_active=0 and a.emp_code in (SELECT distinct emp_code from hrm_separation where separation_type=1 and id in (SELECT MAX(id) from hrm_separation where separated_from between '".$date_range[0]."' and '".$date_range[1]."' group by emp_code))";} // and separated_from like '$con_date'
	else if($cbo_salary_sheet==5){$emp_status="and b.status_active=0 and a.emp_code in (SELECT distinct emp_code from hrm_separation where separation_type=2 and id in (SELECT MAX(id) from hrm_separation where separated_from between '".$date_range[0]."' and '".$date_range[1]."' group by emp_code))";} //and separated_from like '$con_date' 
	else if($cbo_salary_sheet==6){$emp_status="and b.status_active=0 and a.emp_code in (SELECT distinct emp_code from hrm_separation where separation_type=3 and id in (SELECT MAX(id) from hrm_separation where separated_from between '".$date_range[0]."' and '".$date_range[1]."' group by emp_code))";} // and separated_from like '$con_date'
	else if($cbo_salary_sheet==7){$emp_status="and b.status_active=0 and a.emp_code in (SELECT distinct emp_code from hrm_separation where separation_type=4 and id in (SELECT MAX(id) from hrm_separation where separated_from between '".$date_range[0]."' and '".$date_range[1]."' group by emp_code))";} //and separated_from like '$con_date'
	else if($cbo_salary_sheet==8){$emp_status="and b.status_active=0 and a.emp_code in (SELECT distinct emp_code from hrm_separation where separation_type=5 and id in (SELECT MAX(id) from hrm_separation where separated_from between '".$date_range[0]."' and '".$date_range[1]."' group by emp_code))";} //and separated_from like '$con_date'
	else if($cbo_salary_sheet==9){$emp_status="and b.status_active=0 and a.emp_code in (SELECT distinct emp_code from hrm_separation where separation_type=6 and id in (SELECT MAX(id) from hrm_separation where separated_from between '".$date_range[0]."' and '".$date_range[1]."' group by emp_code))";} //and separated_from like '$con_date'
	
	$rpt_com=$company_id;
	$rpt_loc=$location_id;
	
	if ($cbo_emp_category=='') $category ="and b.category in ($employee_category_index)"; else $category ="and b.category='$cbo_emp_category'";
	if ($company_id==0) $cbo_company_id=""; else $cbo_company_id="and a.company_id='$company_id'";
	if ($location_id==0) $location_id=""; else $location_id="and a.location_id='$location_id'";
	if ($division_id==0) $division_id=""; else $division_id="and a.division_id in ($division_id)";
	if ($department_id==0) $department_id=""; else	$department_id="and a.department_id in ($department_id)";
	if ($section_id==0) $section_id=""; else $section_id="and a.section_id in ($section_id)";
	if ($subsection_id==0) $subsection_id=""; else $subsection_id="and a.subsection_id in ($subsection_id)";
	if ($designation_id==0) $designation_id=""; else $designation_id="and a.designation_id in ($designation_id)";
	if ($cbo_salary_based=="") $salary_based=""; else $salary_based="and b.salary_type_entitled='$cbo_salary_based'";
	if ($emp_code=="") $emp_code=""; else $emp_code="and b.emp_code in ('".implode("','",explode(",",$emp_code))."')";
	if ($id_card=="") $id_card_no=""; else $id_card_no="and emp.id_card_no in ('".implode("','",explode(",",$id_card))."')";
	
	/*if ($payment_method==0) $payment_met =""; 
	else if ($payment_method==1) $payment_met =" and emp.bank_gross<1 ";// "and a.emp_code not in (select emp_code from hrm_employee_salary_bank where status_active=1 and is_deleted=0 and lib_bank_id<>'')
	else if ($payment_method==2) $payment_met =" and emp.bank_gross>0 ";//"and a.emp_code in (select emp_code from hrm_employee_salary_bank where status_active=1 and is_deleted=0 and lib_bank_id<>'')";*/
	
	$addrs ="";		
	if($company_details[$company_id]['plot_no'] !='') $addrs .= $company_details[$company_id]['plot_no'].", "; 
	if($company_details[$company_id]['level_no'] !='') $addrs .= $company_details[$company_id]['level_no'].", "; 
	if($company_details[$company_id]['block_no'] !='') $addrs .= $company_details[$company_id]['block_no'].", "; 
	if($company_details[$company_id]['road_no'] !='') $addrs .= $company_details[$company_id]['road_no'].", "; 
	if($company_details[$company_id]['city'] !='') $addrs .= $company_details[$company_id]['city'].", "; 
	
 	//variable settings
	$qr="select * from variable_settings_hrm where status_active=1 and is_deleted=0 and company_name='$company_id' order by id";
	//echo $qr;die;
	$result_qr = mysql_query( $qr ) or die( $qr . "<br />" . mysql_error() );
	while($res=mysql_fetch_array($result_qr))		
	{
		$ot_fraction = $res['allow_ot_fraction'];
		$ot_start_min = $res['ot_start_minute'];
		$one_hr_ot_unit = $res['one_hour_ot_unit'];
		$buyer_ot_min_comp = $res['first_ot_limit'];
	}	

	$date_range=explode("_",$cbo_month_selector);
	$num_of_days=datediff('d', $date_range[0], $date_range[1]);
	$week=0;
	$k=0;
	for($i=0; $i<($num_of_days+7); $i++)
	{
		$todays=add_date($date_range[0],$i); 
		$as = date("D",strtotime($todays));

		if(date("D",strtotime($todays))=="Sat")
		{
			$week++;
			if( $week>4 && date("Y-m",strtotime($date_range[0]))!=date("Y-m",strtotime($todays))) break;
			if($k==0) $start_date=$todays; 
			$week_array[$todays]=$week;
			$weeksts[$week]['start']=$todays;
			$k++;
		}
		
		else
		{
			if( $week>0 )
			{
				$week_array[$todays]=$week;
				$weeksts[$week]['end']=$todays;
			}
		}
		
		
		$to_date=$todays; 
	}
	$num_of_week=$week-1;
	//ob_start();	
	//echo  $one_hr_ot_unit."=".$ot_fraction."=". $ot_start_min;
	$sql="select a.emp_code,b.id_card_no, a.sign_in_time,a.sign_out_time,b.company_id,b.designation_id,b.department_id,b.section_id,CONCAT(b.first_name, ' ',b.middle_name, ' ',b.last_name) AS name,a.total_over_time_min,a.policy_shift_id,a.attnd_date,a.is_next_day,a.is_regular_day, a.status from hrm_attendance a, hrm_employee b where a.emp_code=b.emp_code and a.attnd_date between '".$start_date."' and '".$to_date."' $category $cbo_company_id $location_id $division_id $department_id $section_id $subsection_id $designation_id $salary_based $emp_status";
	//echo $sql; die;
	$sql_exe=mysql_query($sql);
	while($row=mysql_fetch_assoc($sql_exe))
	{

		//print_r( get_buyer_ot_hr(  $row['total_over_time_min'], $one_hr_ot_unit, $ot_fraction, $ot_start_min, 1440) ); die;
		$tmp_ot=explode(".", get_buyer_ot_hr(  $row['total_over_time_min'], $one_hr_ot_unit, $ot_fraction, $ot_start_min, 1440));
		 //echo $row[attnd_date]."*".$row['total_over_time_min']."=";	
		$data[$row['emp_code']][$week_array[$row['attnd_date']]]['oth']+=$tmp_ot[0];
		$data[$row['emp_code']][$week_array[$row['attnd_date']]]['otm']+=$tmp_ot[1];			
		$empInfo[$row['emp_code']]['name'] =  $row['name'];
		$empInfo[$row['emp_code']]['id_card_no'] =  $row['id_card_no'];
		$empInfo[$row['emp_code']]['designation_id'] =  $designation_chart[$row['designation_id']];
		$empInfo[$row['emp_code']]['department_id'] =      $department_details[$row['department_id']];
		$empInfo[$row['emp_code']]['section_id'] =         $section_details[$row['section_id']];
		$shift_time_min=0;
		$tmp_lend=$shift_lists[$row['policy_shift_id']]['lunch_break_ends'];

		if( $row[sign_out_time]!="00:00:00" &&  $row[sign_in_time]!="00:00:00" )
		{
			if( strtotime($row[sign_in_time])<strtotime($shift_lists[$row['policy_shift_id']]['shift_start']) )
				$row[sign_in_time]=$shift_lists[$row['policy_shift_id']]['shift_start'];
			
			
			
			$tmp=$shift_lists[$row['policy_shift_id']]['shift_end'];
			 
			 
			if( $row['is_next_day']==1 )
			{
				$row[sign_in_time]= $row[attnd_date]." ".$row[sign_in_time];
			    $row[sign_out_time]=add_date($row[attnd_date],1)." ".$row[sign_out_time];

				 if($shift_lists[$row['policy_shift_id']]['cross_date']!=1)
					 $tmp=$row[attnd_date]." ".$shift_lists[$row['policy_shift_id']]['shift_end'];
				 else 
				 	$tmp=add_date($row[attnd_date],1)." ".$shift_lists[$row['policy_shift_id']]['shift_end'];
				 
				 if( strtotime($row[sign_out_time])> strtotime($tmp) )
					$row[sign_out_time]=$tmp;
					
				 //$tmp_lend=add_date($row[attnd_date],1)." ".$tmp_lend;
			}
		   $after_lunch=datediff( "n", $tmp, $row[sign_out_time]);
			
				
			
			if($after_lunch<1) $after_lunch=0;
			//-($shift_lists[$row['policy_shift_id']]['lunch_break_min']);	
				
			// Lunch Time Dedution
			if( strtotime( $row[sign_out_time] )> strtotime( $tmp_lend ) )
			{
				$shift_time_min= datediff( "n", $row[sign_in_time],$row[sign_out_time])-($shift_lists[$row['policy_shift_id']]['lunch_break_min']);	
				//echo $shift_time_min."=";
			}
			
			else if ( strtotime( $row[sign_out_time] )> strtotime($shift_lists[$row['policy_shift_id']]['lunch_start']) && strtotime( $row[sign_out_time] )< strtotime( $tmp_lend )  )
			{
				$shift_time_min= datediff( "n", $row[sign_in_time],$shift_lists[$row['policy_shift_id']]['lunch_start']);
				//echo $shift_time_min."=";	
			}
			else
			{
				if( $row['is_next_day']==1 )
					$shift_time_min= datediff( "n", $row[sign_in_time],$row[sign_out_time])-($shift_lists[$row['policy_shift_id']]['lunch_break_min']);
				else
					$shift_time_min= datediff( "n", $row[sign_in_time],$row[sign_out_time]);
				// echo $row[attnd_date]."*".$row[sign_in_time]."=";	
			}
				
				$shift_time_min=$shift_time_min-$after_lunch;
				
//echo  $after_lunch."=";
				//$shift_time_hrs = $shift_time_min/60;
		}
		//echo $row['is_regular_day']."=";
		//('W','GH','FH','CH')
		if( $row['is_regular_day']==0)
		{
			$shift_time_min=0;
		}
	
		//$data[$row['emp_code']][$week_array[$row['attnd_date']]]['working'] +=($shift_time_min/60);
		$data[$row['emp_code']][$week_array[$row['attnd_date']]]['working'] +=($shift_time_min);
	}
	
	//echo "<pre>";
	//print_r( $data ); die;
	
	?>
   
	<style>
		.verticalText {writing-mode: tb-rl;filter: flipv fliph;-webkit-transform: rotate(270deg);-moz-transform: rotate(270deg);}
	</style> 
	<title>current..</title>
<div align="center" style="width:900px">
		<font size="+1"><? echo $company_details[$company_id]['company_name']; ?></font><br />
		<font size="-1"><? echo $addrs; ?></font><br />
		<font size="-1">Weekly OT Hours: <? echo date("F, Y",strtotime($date_range[0])); ?> </font>
</div><?php //echo $num_of_week; die;?>
	<table cellpadding="0" cellspacing="0" width="<?php echo (840)+$num_of_week*2*50; ?>" class="rpt_table" style="font-size:12px; border:1px solid #000000" rules="all"> 
		<thead>
			<tr class="tbl_top">
				<th colspan="<?php echo 9+($num_of_week*2);?>">Month of <? echo date("F, Y",strtotime($date_range[0])); ?> </th>
			</tr>
			<tr class="tbl_top">
				<th colspan="7">General Information</th>
                <?		
				for($j=1; $j<=$num_of_week;$j++)
				{
					?>                        
					<th width='100' colspan="2"><b><? echo date("d",strtotime($weeksts[$j]['start']))."-".date("d",strtotime($weeksts[$j]['end']));?></b></th>
					<? 
				} 
				?> 
				<th colspan="2" width="100">Total</th>                           
			</tr>
			<tr class="tbl_top">
				<th width="30"><b>SL</b></th>
				<th width="100"><b>Emp ID</b></th>
				<th width="70"><b>Emp Code</b></th>
				<th align="100"><b>Employee Name</b></th>
				<th align="100"><b>Designation</b></th>
				<th align="100"><b>Depertment</b></th>
				<th align="100"><b>Section</b></th>
                          
                <?
                for($j=1;$j<=$num_of_week;$j++)
                {   
                    ?>
                    <th width="50">WH</th>
                    <th width="50">OT</th>
                    <?
                }
                ?>  
				<th align="60"><b>Total WH</b></th>
				<th align="60"><b>Total OT</b></th>
			</tr>
		</thead>
        <tbody>
		<?
		$sl=1;
		foreach($data as $emp=>$edata)
		{
			$whArr=array();
			$flag=0;
			$Twh = 0;
			$Tot = 0;
			for($j=1;$j<=$num_of_week; $j++)
			{   
				$ot_mnt		= $data[$emp][$j]['working'];
				$tmp_hrs	= floor($ot_mnt/60);
				$tmp_mnts	= $ot_mnt-($tmp_hrs*60);
				$whArr[$j]['wh']=$tmp_hrs.".".str_pad($tmp_mnts,2,"0",STR_PAD_LEFT);
				
				$data[$emp][$j]['oth'];
				$tmpHrs=floor($data[$emp][$j]['otm']/60);
				$tmpMnt=$data[$emp][$j]['otm']-($tmpHrs*60);
				$whArr[$j]['ot']=$data[$emp][$j]['oth']+$tmpHrs.".".str_pad($tmpMnt,2,"0",STR_PAD_LEFT);
				
				if($whArr[$j]['wh']>48 || $whArr[$j]['ot']>24)
				{
					$flag=1;
				}
			}

			if($flag==1)
			{
				?>
                <tr>
                    <td> <? echo $sl++; ?></td>
                    <td> <? echo $empInfo[$emp]['id_card_no']; ?></td>
                    <td> <? echo $emp; ?></td>
                    <td> <? echo $empInfo[$emp]['name'];?></td>
                    <td> <? echo $empInfo[$emp]['designation_id']; ?></td>
                    <td> <? echo $empInfo[$emp]['department_id'];?></td>
                    <td> <? echo $empInfo[$emp]['section_id']; ?></td>
                    <?
					$gdtot = 0;
                    for($j=1;$j<=$num_of_week; $j++)
                    {   
                        ?>
                        <td width="50" align="right"><? echo $whArr[$j]['wh']; ?>&nbsp;</td>
                        <td width="50" align="right"><? echo $whArr[$j]['ot']; ?>&nbsp;</td>
                        <?
						$expWrkHr=array();
						$expWrkHr=explode(".", $whArr[$j]['wh']);
						$grandTotal[$j]['wHr']+=$expWrkHr[0];
						$grandTotal[$j]['wMnt']+=$expWrkHr[1];
						
						$totWrkHr+=$expWrkHr[0];
						$totWrkMnt+=$expWrkHr[1];
						
						$gTotWrkHr+=$expWrkHr[0];
						$gTotWrkMnt+=$expWrkHr[1];
						
						
						$expOtHr=array();
						$expOtHr=explode(".", $whArr[$j]['ot']);
						$grandTotal[$j]['wOtHr']+=$expOtHr[0];
						$grandTotal[$j]['wOtMnt']+=$expOtHr[1];
						
						$totOtHr+=$expOtHr[0];
						$totOtMnt+=$expOtHr[1];
						
						$gTotOtHr+=$expOtHr[0];
						$gTotOtMnt+=$expOtHr[1];
                    }
					$tmpTWrHr	= floor($totWrkMnt/60);
					$tmpTWrMnt	= $totWrkMnt-($tmpTWrHr*60);
					$totWorkingHr=$totWrkHr+$tmpTWrHr.".".str_pad($tmpTWrMnt,2,"0",STR_PAD_LEFT);
					
					$tmpTOtHr	= floor($totOtMnt/60);
					$tmpTOtMnt	= $totOtMnt-($tmpTOtHr*60);
					$totOvertimeHr=$totOtHr+$tmpTOtHr.".".str_pad($tmpTOtMnt,2,"0",STR_PAD_LEFT);
					
					$totWrkHr=0;
					$totWrkMnt=0;
					$totOtHr=0;
					$totOtMnt=0;
                    ?> 
                    <td width="50" align="right"><? echo $totWorkingHr; ?>&nbsp;</td>
                    <td width="50" align="right"><? echo $totOvertimeHr; ?>&nbsp;</td> 
                </tr>
            	<?
			}
		}
		?>
        </tbody>
        <tfoot>
        	<tr style="font-weight:bold" bgcolor="#CCCCCC">
				<td colspan="7" align="center">GRAND TOTAL</td>
				<? 
				for($j=1;$j<=$num_of_week; $j++)
                {
					$tmpGWrHr	= floor($grandTotal[$j]['wMnt']/60);
					$tmpGWrMnt	= $grandTotal[$j]['wMnt']-($tmpGWrHr*60);
					$gWrArr[$j]['wh']=$grandTotal[$j]['wHr']+$tmpGWrHr.".".str_pad($tmpGWrMnt,2,"0",STR_PAD_LEFT);
					
					$tmpGOtHr	= floor($grandTotal[$j]['wOtMnt']/60);
					$tmpGOtMnt	= $grandTotal[$j]['wOtMnt']-($tmpGOtHr*60);
					$gWrArr[$j]['ot']=$grandTotal[$j]['wOtHr']+$tmpGOtHr.".".str_pad($tmpGOtMnt,2,"0",STR_PAD_LEFT);
					?>
                    <td width="50" align="right"><? echo $gWrArr[$j]['wh']; ?>&nbsp;</td>
                    <td width="50" align="right"><? echo $gWrArr[$j]['ot']; ?>&nbsp;</td>
                    <?php
				}
				
				$tmpGTWrHr	= floor($gTotWrkMnt/60);
				$tmpGTWrMnt	= $gTotWrkMnt-($tmpGTWrHr*60);
				$gTotWorkingHr=$gTotWrkHr+$tmpGTWrHr.".".str_pad($tmpGTWrMnt,2,"0",STR_PAD_LEFT);
				
				$tmpGTOtHr	= floor($gTotOtMnt/60);
				$tmpGTOtMnt	= $gTotOtMnt-($tmpGTOtHr*60);
				$totOvertimeHr=$gTotOtHr+$tmpGTOtMnt.".".str_pad($tmpGTOtMnt,2,"0",STR_PAD_LEFT);
				
                ?>
				<td align="right"><? echo $gTotWorkingHr; ?>&nbsp;</td>	
				<td align="right"><? echo $totOvertimeHr; ?>&nbsp;</td>              
			</tr>
        </tfoot>
	</table>
	<?php
	$html = ob_get_contents();
	ob_clean();	
	
	foreach (glob( "tmp_report_file/"."*.xls") as $filename) {			
		@unlink($filename);
	}		
		//html to xls convert
	$name=time();
	$name="$name".".xls";	
	$create_new_excel = fopen('tmp_report_file/'.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);	
	
		//echo "$html"."####"."$name";
	echo "$html"."####"."$name"."####".$samary;		
	exit();
	//$samary
}

function add_date($orgDate,$days){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
  return $retDAY;
}
function return_field_value($fdata,$tdata,$cdata){

	$sql_data="select $fdata from  $tdata where $cdata";
	$sql_data_exe=mysql_query($sql_data);
	$sql_data_rslt=mysql_fetch_array($sql_data_exe);
	$m_data  = $sql_data_rslt[0];

	return $m_data ;

}
?>