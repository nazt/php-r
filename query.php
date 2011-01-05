<?php
  $index = getIndex();
  // print_r($index);

  printf ("%33s\n", "----- Raw matrix -----");
  printMatrix($index);
  normalize($index);
  newline(3);
  printf ("%40s\n", "----- Normalized matrix -----");
  printMatrix($index);
  printf ("%s\n\n", "==========================================");
  printf("\t*** %s ***\n", "Similarity Score");
  print_calculated_score($argv[1], $argv[2], $index['dictionary']);
  printf ("%s\n", "==========================================");
  printf ("\t%s\n", "== DEBUG ==");
  print_calculated_score('doc1', 'doc2', $index['dictionary']);
  print_calculated_score('doc1', 'doc3', $index['dictionary']);
  print_calculated_score('doc2', 'doc1', $index['dictionary']);
  newline();

function print_calculated_score ($docA, $docB, $dict) {
  $cosine_score = cosineSim($docA, $docB, $dict);
  printf(" -- cos (%s & %s) = %.2f%% [%f]\n", $docA, $docB, $cosine_score, $cosine_score);
}
function printMatrix($index) {
  	/* print docID */
    printf("%10s",'vocab/doc');
    foreach ($index['docCount'] as $key => $value) {
      printf('%10s ', $key);
    }
    newline();
    /* print tf for each doc  value */
  	foreach ($index['dictionary'] as $key => $value) {
      printf("%10s",$key);
      foreach ($index['docCount'] as $docID => $value) {
        printf("%10.2f ",$index['dictionary'][$key]['postings'][$docID]['tf']);
      }
      newline();
  	}
} 

function cosineSim($docA, $docB, $dict) {
        $result = 0;
        foreach ($dict as $term => $entry) {
          $tf[0] = (float)$entry['postings'][$docA]['tf'];
          $tf[1] = (float)$entry['postings'][$docB]['tf'];
          $product = $tf[0] * $tf[1];
          $result += $product;
        }
        return $result;
}

function normalize(&$index) { 
  $sum_square = sum_square($index['dictionary']);
  foreach ($index['dictionary'] as $vocab => $entry) {
    foreach ($entry['postings'] as $docID => $value) {
      $index['dictionary'][$vocab]['postings'][$docID]['tf'] = $value['tf']/sqrt($sum_square[$docID]);
    }
  }
}
 
function sum_square($doc) {
  $sum_square = array();
  foreach ($doc as $vocab => $entry) {
    foreach ($entry['postings'] as $docID => $value) {
      $sum = $value['tf'] * $value['tf'];
      $sum_square[$docID] += $sum;
    }
  }
  return $sum_square;
}

function collection($value='') {
  $collection = array();
}

function getIndex() {
	$collection = array(
	        'doc1' => 'My name is nat weerawan',
	        'doc2' => 'my nat weerawan',
	        'doc3' => 'I am an opendreamer'
	);
	$dictionary = array();
	$docCount = array();
	foreach($collection as $docID => $doc) {
    $doc = strtolower($doc);
		$terms = explode(' ', $doc);
		$docCount[$docID] = count($terms);
		foreach($terms as $term) {
			if(!isset($dictionary[$term])) {
			  $dictionary[$term] = array('df' => 0, 'postings' => array());
			}
			if(!isset($dictionary[$term]['postings'][$docID])) {
			  $dictionary[$term]['df']++;
			  $dictionary[$term]['postings'][$docID] = array('tf' => 0);
			}
			$dictionary[$term]['postings'][$docID]['tf']++;
		}
	 //		echo "------------------------------------------------------------ \n";
	}
	return array('docCount' => $docCount, 'dictionary' => $dictionary);
}

function getTfidf($term) {
        $index = getIndex();
        $docCount = count($index['docCount']);
        $entry = $index['dictionary'][$term];
        foreach($entry['postings'] as  $docID => $postings) {
                echo "Document $docID and term $term give TFIDF: " .
                        ($postings['tf'] * log($docCount / $entry['df'], 2));
                echo "\n";
        }
}

function newline($value='1') {
  for ($i=0; $i < $value; $i++) { 
    echo "\n";
  }
}
?>
