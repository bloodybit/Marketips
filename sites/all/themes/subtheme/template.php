<?php

/**
 * @file
 * template.php
 */

 /**
  * Theme function implementation for bootstrap_search_form_wrapper.
  */
 function subtheme_subtheme_search_form_wrapper($variables) {

   $output = '
   <ul class="nav navbar-nav navbar-right">
    <li class="expanded dropdown">
       <a href="#" title data-target="#" class="dropdown-toggle nolink" data-toggle="dropdown">
        <i title="glyphicon-search" class="icon glyphicon glyphicon-search" aria-hidden="true"></i>
        <span class="caret"></span>
      </a>
      <ul class="dropdown-menu" style="padding: 15px; min-width: 250px;">
        <li class="first leaf">
          <div class="row">
            <div class="col-md-12">';

   $output = '<div class="input-group">';
   $output .= $variables['element']['#children'];
   $output .= '<span class="input-group-btn">';
   $output .= '<button type="submit" class="btn btn-default">';
   // We can be sure that the font icons exist in CDN.
   if (theme_get_setting('bootstrap_cdn')) {
     $output .= _bootstrap_icon('search');
   }
   else {
     $output .= t('Search');
   }
   $output .= '</button>';
   $output .= '</span>';
   $output .= '</div>';

   $output .= '
             </div>
           </div>
         </li>
       </ul>
     </li>
   </ul>';

   return $output;
 }


// function subtheme_form_alter(&$form, &$form_state, $form_id) {
//   if($form_id) {
//     switch ($form_id) {
//       case 'search_form':
//         dpm($form);
//         $form['basic']['keys']['#theme_wrappers'] = array('subtheme_search_form_wrapper');
//         break;
//     }
//   }
// }
//
// function subtheme_theme() {
//   $hooks = array(
//       'subtheme_search_form_wrapper' => array(
//       'render element' => 'element',
//       'file' => 'subtheme-search-form-wrapper.func.php',
//     ),
//   );
//   return $hooks;
// }

function subtheme_preprocess_node(&$variables) {
  $variables['theme_hook_suggestions'][] = 'node__' . $variables['type'] . '__' . $variables['view_mode'];
}

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

global $user;
global $tag;

print_r($tag);

//  // SETTING SOME COOKIES IN ORDER TO RECOGNISE THE user
//  if(isset($COOKIE['parseId']) && $COOKIE['parseId']!=null){
//    $cookie = $COOKIE['parseId'];
//    setcookie('parseId', $cookie, time()+3600*24*60); //per 2 mesi
//  } else {
//    $parseId = rand(9999999999999,1);
//    $cookie = $parseId;
//    setcookie('parseId', $parseId, time()+3600*24*60); //per 2 mesi
//  }


//Setting array parse with variables I will need in javascript
$parse = array(
  //'parseId' => $cookie,
  'uid' => $user->uid,
  'tag' => $tag,
  'title' => drupal_get_title(),
  'debug' => false
);

drupal_add_js(array('parse'=>$parse), 'setting');

?>
