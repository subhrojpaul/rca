<?php
include "../assets/utils/fwdbutil_g.php";
$q="select q.question_id, q.question_code, q.question_status, q.updated_date, q.solution_id, replace(replace(approver_comments, '\n', ''), '\r', '') approver_comments, q.sub_article_id, sub_article_code, corr_ans.answer_id correct_answer_id, corr_ans.answer_code correct_answer_code, corr_ans_img.image_id correct_answer_image_id, corr_ans_img.image_name correct_answer_image_name, concat(corr_ans_img.image_path, '/', corr_ans_img.generated_file_name) correct_answer_image, qimg.image_id question_image_id, qimg.image_name question_image_name, concat(qimg.image_path, '/',qimg.generated_file_name) question_image, q.question_owner_id, qown.owner_code, own.user_id, own.email, concat(own.fname, ' ', own.lname) owner_name, q.level_of_topic, q.level_of_interpretation, q.question_type, q.question_source, q.time_alloted_secs, subart.sub_article_name, subart.article_code, subart.article_name, subart.topic_code, subart.topic_name, subart.chapter_code, subart.chapter_name, subart.area_code, subart.area_name, subart.subject_code, subart.subject_desc, group_concat(rej.reason_code) rej_codes_concat from sh.questions q left outer join sh.question_reject_reason rej on q.question_id = rej.question_id, sh.question_answers corr_ans, sh.sub_article_hierarchy subart, sh.question_answer_images qimg, sh.question_answer_images corr_ans_img, sh.question_owners qown, sh.user_info own where 1=1 and q.question_image_id = qimg.image_id and q.sub_article_id = subart.sub_article_id and q.answer_id = corr_ans.answer_id and corr_ans.answer_image_id = corr_ans_img.image_id and q.question_owner_id = qown.owner_id and qown.linked_user_id = own.user_id group by q.question_id limit 100000";
$dbh=setupPDO();
$r=runQueryAllRows($dbh, $q, array());
echo " done with query all rows", "<br>";
//print_r($r);
echo "Total rows ", count($r);
echo " printed results", "<br>";
?>
