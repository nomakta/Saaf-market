<?php 
class Forum{

	private $db;

    function __construct($DB_con, User $user)
    {
      $this->db = $DB_con;
	  $this->user = $user;
    }

    public function GetForumCategories() 
    {
    	$array = array();
    	$a = $this->db->prepare("SELECT * FROM forum_categories WHERE cato_enabled='1'");
    	$a->execute();
    	if($a->Rowcount() > 0) {
    		foreach($a as $cato) {
			  $topics = $this->db->prepare("SELECT * FROM forum_topics WHERE cato_id='".$cato['id']."'");
              $topics->Execute();
              if($topics->Rowcount() == 0) { $TopicAmount = "No topics posted"; }else{ $TopicAmount = $topics->Rowcount(); }

       	      $replies = $this->db->prepare("SELECT * FROM forum_replies WHERE cato_id='".$cato['id']."'");
          	  $replies->Execute();
       	      if($replies->Rowcount() == 0 ) { $ReplyAmount = "No replies posted"; }else{ $ReplyAmount = $replies->Rowcount(); }

       	      $newest1 = $this->db->prepare("SELECT * FROM forum_topics WHERE cato_id='".$cato['id']."' ORDER BY topic_posted DESC LIMIT 1");
          	  $newest1->Execute();
            
           	  $newest2 = $this->db->prepare("SELECT * FROM forum_replies WHERE cato_id='".$cato['id']."' ORDER BY reply_Date DESC LIMIT 1");
          	  $newest2->Execute();

          	  if($newest1->Rowcount() == 0) { $latestpost = 'n/a'; }else{ 
          	  	if($newest2->Rowcount() == 0 ) { foreach($newest1 as $one) { $latestpost = $one['topic_posted']; } }else{
          	  		foreach($newest1 as $one) {
          	  			foreach($newest2 as $two) {
          	  				$date1 = $one['topic_posted'];
          	  				$date2 = $two['reply_Date'];

          	  				if($date1 < $date2) { $latestpost = $date2;
          	  				}elseif($date2 < $date1) { $latestpost = $date1; }
          	  			}
          	  		}
          	  	}
          	  } 

          	  $cato_array = array('id' => $cato['id'], 'name' => $cato['cato_name'], 'desc' => $cato['cato_desc'], 'topics' => $TopicAmount, 'replies' => $ReplyAmount, 'latest_post' => $latestpost);
          	  $array[] = $cato_array;
    		}
    	}else{
			$error = array('Message_type' => 'Error', 'Message' => 'No categories found');
            $array[] = $error;
    	}
    	return $array;
    }

    public function GetTopics($category) 
    {
    	$array = array();
    	$a = $this->db->prepare('SELECT * FROM forum_topics WHERE cato_id="'.$category.'"');
    	$a->Execute();

    	if($a->Rowcount() > 0) {
    		foreach($a as $topic) {
    			$b = $this->db->prepare('SELECT * FROM forum_replies WHERE topic_id="'.$topic['id'].'"');
    			$b->Execute();
    			if($b->Rowcount() == 0 ) { $latestReply = $topic['topic_posted']; }else{ foreach($b as $date) { $latestReply = $date['reply_date']; }}

    			$topic_array = array('id' => $topic['id'], 'title' => $topic['topic_name'], 'author' => $topic['posted_by'], 'date_posted' => $topic['topic_posted'], 'latest_post'=>$latestReply, 'locked' => $topic['topic_locked'], 'pinned' => $topic['topic_pinned']);
    			$array[] = $topic_array;
    		}
    	}else{
			$error = array('Message_type' => 'Error', 'Message' => 'No topics found');
            $array[] = $error;
    	}
    	return $array;
    }

    public function GetTopic($TopicID)
    {
    	$array = array();
    	$a = $this->db->prepare("SELECT * FROM forum_topics where id='".$TopicID."'");
    	$a->Execute();

    	if($a->Rowcount() > 0) {
    		foreach($a as $topic) {
    			$topic_array = array('id' => $topic['id'], 'title' => $topic['topic_name'], 'text' => $topic['topic_text'], 'author' => $topic['posted_by'], 'date_posted'=> $topic['topic_posted'], 'locked' => $topic['topic_locked'], 'pinned' => $topic['topic_pinned']);
    			$array[] = $topic_array;
    		}
    	}else{
			$error = array('Message_type' => 'Error', 'Message' => 'Topic not found');
            $array[] = $error;
    	}
    	return $array;
    }

    public function GetTopicReplies($TopicID) 
    {
   		$array = array();
   		$a = $this->db->prepare("SELECT * FROM forum_replies WHERE topic_id='".$TopicID."'");
   		$a->Execute(); 

   		if($a->Rowcount() > 0) {
   			foreach($a as $reply) {
   				$reply_array = array('id' => $reply['id'], 'message' => $reply['reply_text'], 'date_posted' => $reply['reply_date'], 'posted_by' => $user->GetUsername($reply['user_id']));
   				$array[] = $reply_array;	
   			}
   		}else{
			$error = array('Message_type' => 'Error', 'Message' => 'No replies found');
            $array[] = $error;
   		}
    }
}