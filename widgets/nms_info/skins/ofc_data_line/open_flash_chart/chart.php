<html>
<head>
<script type="text/javascript" src="js/swfobject.js"></script>
</head>
<body>

<?php

// generate some random data
srand((double)microtime()*1000000);

//
// NOTE: how we are filling 3 arrays full of data,
//       one for each line on the graph
//
$data_1 = array();
$data_2 = array();
$data_3 = array();
for( $i=0; $i<12; $i++ )
{
  $data_1[] = rand(14,19);
  $data_2[] = rand(8,13);
  $data_3[] = rand(1,7);
}


include_once( 'ofc-library/open-flash-chart.php' );
$g = new graph();
$g->title( 'Many data lines', '{font-size: 20px; color: #736AFF}' );

$g->width = "600";
$g->height = "300";

// we add 3 sets of data:
$g->set_data( $data_1 );
$g->set_data( $data_2 );
$g->set_data( $data_3 );

// we add the 3 line types and key labels
$g->line( 2, '0x9933CC', 'Page views', 10 );
$g->line_dot( 3, 5, '0xCC3399', 'Downloads', 10);    // <-- 3px thick + dots
$g->line_hollow( 2, 4, '0x80a033', 'Bounces', 10 );

$g->set_x_labels( array( 'January','February','March','April','May','June','July','August','Spetember','October','November','December' ) );
$g->set_x_label_style( 10, '0x000000', 0, 2 );

$g->set_y_max( 20 );
$g->y_label_steps( 4 );
$g->set_y_legend( 'Open Flash Chart', 12, '#736AFF' );
$g->set_output_type('js');
echo $g->render();
?>

</body>
</html>