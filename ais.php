<?php
class survey {

 private $database;
 private $degrees=array(1=>"strongly disagree", "disagree", "neutral or no opinion", "agree", "strongly agree");

 function __construct($db) { # $db is a mysqli object
  $this->database = $db;  
  $result = $db->query("select table_name from information_schema.tables where table_name='answer'");
  $row = $result->fetch_assoc();
  $result->close();
  if (!$row) {
   $db->query(<<<I
    create table ideology
    (
     id integer key auto_increment,
     ideology varchar(40),
     created date
    )
I
   );
   $db->query(<<<S
    create table section
    (
     id integer key,
     section varchar(256)
    )
S
   );
   $db->query(<<<Q
    create table question
    (
     id integer key,
     section integer,
     question varchar(384)
    )
Q
   );
   $db->query(<<<A
    create table answer
    (
     ideology integer,
     question integer,
     answer integer
    )
A
   );
   $db->query(<<<AI
    create index answer_ideology
    on answer (ideology)
AI
   );
   $db->query(<<<AQ
    create index answer_question
    on answer(question)
AQ
   );
   $db->query(<<<AA
    create index answer_answer
    on answer(answer)
AA
   );
   $db->query(<<<VS
    create view survey as
    select section.id as section_id, section.section as section, question.id as question_id, question.question as question
    from section, question
    where section.id=question.section
    order by section.id, question.id
VS
   );
   $db->query(<<<VD
    create view dsquared as
    select a.ideology id1, b.ideology id2, ideology.ideology ideology2, sum(power(a.answer-b.answer,2)) dsquared
    from answer as a, answer as b, ideology
    where a.question=b.question
    and ideology.id=b.ideology
    and b.ideology<>a.ideology
    group by id1, id2
    order by dsquared
VD
   );
   $db->query(<<<VC
    create view conflict as
    select a.ideology id1, b.ideology id2, a.question question, a.answer ans1, b.answer ans2, -(a.answer-3)*(b.answer-3) conflict
    from answer as a, answer as b
    where a.question=b.question
    and a.ideology<b.ideology
VC
   );
   $db->query(<<<VTC
    create view total_conflict as
    select a.id1 id1, a.id2 id2, b.ideology ideology2, sum(a.conflict) conflict
    from conflict a, ideology b
    where a.id2 = b.id
    group by id1, id2
    union
    select a.id2, a.id1, b.ideology, sum(a.conflict) conflict
    from conflict a, ideology b
    where a.id1 = b.id
    group by a.id2, a.id1
VTC
   );
   $db->query(<<<VSM
    create view sums as
    select question q, avg(answer) sm, stddev(answer) ssd
    from answer
    group by q
VSM
   );
   $db->query(<<<VSS
    create view ss  as # standard score
    select answer.question q, answer.ideology i, answer.answer x, (answer.answer-sums.sm)/ssd ss
    from answer, sums
    where answer.question=sums.q
VSS
   );
   $db->query(<<<VP
    create view ppmcc as # pearson product-moment correlation coefficient
    select sa.q q1, sb.q q2, sum(ssa.ss*ssb.ss)/count(*) r
    from sums sa, sums sb, ss ssa, ss ssb
    where sa.q=ssa.q
    and sb.q=ssb.q
    and sa.q<sb.q
    and ssa.i=ssb.i
    group by q1, q2
    order by r
VP
   );


  }
 }

 function generate_questionnaire() {
  $db = $this->database;
  $result = $db->query("select id, section from section order by id");
  while ($row = $result->fetch_assoc()) {
   echo "<fieldset>\n";
   echo "<legend class=\"section\">Section {$row['id']}: {$row['section']}</legend>\n";
   $inner_result = $db->query("select id, question from question where section={$row['id']} order by id");
   while ($inner_row = $inner_result -> fetch_assoc()) {
    $id = $inner_row["id"];
    echo "<fieldset>\n";
    echo "<legend class=\"question\">{$inner_row['question']}</legend>\n";
    for ($k = 1; $k <= 5; $k++) {
     echo "<label class=\"deg$k\">\n{$this->degrees[$k]}";
     echo "<input type=\"radio\" name=\"$id\" value=\"$k\" class=\"deg$k\"";
     if ($k == 3) echo " checked required"; # see http://stackoverflow.com/a/8287947/1269964
     echo " />\n"; # finish generating input element
     echo "</label>\n";
    }
    echo "</fieldset>\n";
   }
   echo "</fieldset>\n";
  }
  $result->close();
 }

 function tabulate($post) {
  $db = $this->database;
  echo "<!-- About to insert \"{$post['ideology']}\" into ideology table -->\n";
  $insert = $db->query("insert into ideology(ideology, created) values (\"{$post['ideology']}\", now())");
  echo "<!-- ".$db -> error." -->\n";
  echo "<!-- Ideology added to table -->\n";
  $max = $db->query("select max(id) id from ideology")->fetch_assoc();
  echo "<!-- ID of new ideology: {$max['id']} -->\n";
  $ideology = $max["id"];
  foreach($post as $question=>$answer) {
   if (is_numeric($question)) {
    $db->query("insert into answer values ($ideology, $question, $answer)");
   }
  }
  # redirect to map page h/t http://www.web-source.net/html_redirect.htm
  echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=map.php?ideology=$ideology\" />\n";
 }

 function friends_and_foes($id, $fof) {
  $db = $this->database;
  $order = (($fof === "foe") ? "desc" : "asc");
  echo "<!-- ideologies in $order order. -->\n";
  $list = $db->query(<<<FF
   select *
   from total_conflict
   where id1=$id
   order by conflict $order
   limit 20
FF
  );
  echo "<!-- ".$db->error." -->\n";
  echo "<ol>\n";
  while ($row = $list->fetch_assoc()) {
   $id2 = $row["id2"];
   $ideology2 = $row["ideology2"] ? $row["ideology2"] : "(ideology #$id2)";
   echo "<li><a href=\"compare.php?id1={$row['id1']}&amp;id2={$row['id2']}\">$ideology2</a></li>\n";
  }
  echo "</ol>\n";
  $list->close();
 }

 function ideology_name($key) {
  $db = $this->database;
  $result = $db->query("select ideology from ideology where id=$key");
  if ($result) {
   $row = $result->fetch_assoc();
   return $row ? $row["ideology"] : "(ideology #$key)";
  }
  return null;
 }

 function dummy_radio($answer) {
  $unchecked = "<input type=\"radio\" disabled ";
  $checked = $unchecked . "checked />\n";
  $unchecked .= " />\n";
  echo "<td>";
  echo str_repeat($unchecked, $answer-1);
  echo $checked;
  echo str_repeat($unchecked, 5-$answer);
  echo "</td>";
 }

 function compare($id1, $id2) {
  $db = $this->database;
  $result = $db->query(<<<COMP
   select a.id qid, a.question question, b.ans1 ans1, b.ans2 ans2, b.conflict conflict
   from question a, conflict b
   where a.id = b.question
   and b.id1=$id1
   and b.id2=$id2
   order by conflict desc
COMP
  );
  $ideology1 = $this->ideology_name($id1);
  $ideology2 = $this->ideology_name($id2);
  echo "<table>\n";
  echo "<caption>Comparison of <a href=\"map.php?ideology=$id1\">$ideology1</a>";
  echo " and <a href=\"map.php?ideology=$id2\">$ideology2</a></caption>\n";
  echo "<colgroup>\n";
  echo "<col style=\"width:70%\" />\n";
  echo "<col class=\"radio\" />\n";
  echo "<col class=\"radio\" />\n";
  echo "</colgroup>\n";
  echo "<thead><tr><th>survey item</th><th>$ideology1</th><th>$ideology2</th></tr></thead>\n";
  echo "<tbody>\n";
  while ($row = $result->fetch_assoc()) {
   echo "<!-- raw conflict is {$row['conflict']} -->\n";
   $class = "conflict" . ($row["conflict"]+4);
   echo "<tr class=\"$class\">\n";
   $question = strip_tags($row["question"]);
   echo "<td><a href=\"question.php?q={$row['qid']}\">$question</a></td>\n";
   $this->dummy_radio($row["ans1"]);
   $this->dummy_radio($row["ans2"]);
   echo "</tr>\n";
  }
  echo "</tbody></table>\n";
 }

 function question_text($q) {
  echo "<!-- entered question_text() -->\n";
  $db = $this->database;
  $result = $db->query("select question from question where id=$q");
  echo $db->error;
  $row = $result->fetch_assoc();
  return $row["question"];
 }

 function bar_graph($q) {
  $db = $this->database;
  $result = $db->query("select count(*) n from ideology");
  $row = $result->fetch_assoc();
  $n = $row["n"];
  $result = $db->query("select answer, count(*) votes from answer where question = $q group by answer");
  $votemax=0;
  while ($row = $result->fetch_assoc()) {
   if (($v = $row["votes"]) > $votemax) $votemax = $v;
   $votes[$row["answer"]] = $v;
  }
  echo "<dl>\n";
  for ($k=5; $k>0; $k--) {
   $votecount = isset($votes[$k]) ? $votes[$k] : 0;
   $pct = round(100.0*$votecount/$n, 1);
   $width = 69.0*$votecount/$votemax;
   $answertext = $this->degrees[$k];
   echo "<dt><em>$votecount</em> said \"$answertext\"</dt><dd style=\"width:$width%\"></dd>\n";
  }
  echo "</dl>\n";
 }

 function correlate_question($q) {
  $db = $this->database;
  $result = $db->query(<<<QCORR
   select a.id q, a.question question, b.r r
   from question a, ppmcc b
   where (a.id=b.q2 and b.q1=$q)
   or (a.id=b.q1 and b.q2=$q)
   order by abs(r) desc
QCORR
  );
  # echo "<!-- ".$db->error." -->\n";
  echo "<h3>propositions most strongly correlated with the above:</h3>\n";
  echo "<table><thead><tr class=\"corr\"><th></th><th class=\"qtext\"></th></tr></thead>\n";
  echo "<!--\n";
  print_r($row);
  echo "-->\n";
  while ($row = $result->fetch_assoc()) {
   $corr = $row["r"];
   $green = $corr>0 ? 255 : round(255-255*abs($corr));
   $red = $corr<0 ? 255 : round(255-255*abs($corr));
   echo "<tr style=\"background:rgb($red, $green, 0)\">";
   echo "<td>";
   echo $corr>=0 ? "+" : "";
   echo number_format($corr, 6)."</td>";
   $question = strip_tags();
   echo "<td>";
   echo "<a href=\"question.php?q={$row['q']}\">";
   echo strip_tags($row['question']);
   echo "</a></td></tr>\n";
  }
  echo "</table>\n";
 }

 function recent($n) {
  $db = $this->database;
  $result = $db->query("select * from ideology order by created desc limit $n");
  while ($row = $result->fetch_assoc()) {
   $id = $row["id"];
   $ideology = $row["ideology"] ? $row["ideology"] : "(ideology #$id)";
   echo "<div class=\"date\">{$row['created']}</div><div class=\"ideology\"><a href=\"map.php?ideology=$id\">$ideology</a></div>\n";
  }
 }

 function feed_items($n) {
  $db = $this->database;
  $result = $db->query("select * from ideology order by created desc limit $n");
  while ($row = $result->fetch_assoc()) {
   $id = $row["id"];
   $ideology = $row["ideology"] ? $row["ideology"] : "(ideology #$id)";
   $created = new DateTime($row["created"]);
   $pubDate = $created->format(DateTime::RFC822);
   $doc = str_replace("feed/", "map.php", $_SERVER["REQUEST_URI"]);
   $url = "http://{$_SERVER['HTTP_HOST']}$doc?ideology=$id";
   echo <<<ITEM
    <item>
    <title>$ideology</title>
    <link>$url</link>
    <description>$ideology, added {$row['created']}</description>
    <pubDate>$pubDate</pubDate>
    <guid>$url</guid>
    </item>
ITEM
   ;
  }
 }

 function spool_data_file() {
  $db = $this->database;
  shell_exec("rm -f ais.zip");
  shell_exec("rm -f /var/tmp/answer.txt /var/tmp/ideology.txt /var/tmp/question.txt");
  $db->query("select 'ideology', 'question', 'answer' union select * into outfile '/var/tmp/answer.txt' from answer");
  $db->query("select 'id', 'ideology', 'created' union select * into outfile '/var/tmp/ideology.txt' from ideology");
  $db->query("select 'id', 'section', 'question' union select * into outfile '/var/tmp/question.txt' from question");
  shell_exec('zip ais.zip /var/tmp/answer.txt /var/tmp/ideology.txt /var/tmp/question.txt');
 }
}

?>


