<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('admin');

$t = new lime_test(12);

dm::loadHelpers(array('Dm'));

$scriptName = $helper->get('request')->getRelativeUrlRoot();
$t->diag('Current cli script name = '.$scriptName);

$expected = $helper->get('controller')->genUrl('@homepage');
$t->is(£link()->getHref(), $expected, 'empty source is '.$expected);
$t->is(£link()->getHref('@homepage'), $expected, 'homepage href is '.$expected);

$expected = $helper->get('controller')->genUrl('dmAuth/signin');
$t->is(£link('+/dmAuth/signin')->getHref(), $expected, '+/dmAuth/signin href is '.$expected);

$expected = $helper->get('controller')->genUrl('dmAuth/signin');
$t->is($helper->get('helper')->£link('+/dmAuth/signin')->getHref(), $expected, 'with helper service, +/dmAuth/signin href is '.$expected);

$frontScriptName = $helper->get('script_name_resolver')->get('front');

$t->is(£link('app:front')->getHref(), $frontScriptName, $frontScriptName);

$t->is(£link('app:front/test')->getHref(), $expected = $frontScriptName.'/test', $expected);

$t->is(£link('app:front/test?var1=val1&var2=val2')->getHref(), $expected = $frontScriptName.'/test?var1=val1&var2=val2', $expected);

$t->comment('Create a test page');

$page = dmDb::create('DmPage', array(
  'module'  => dmString::random(),
  'action'  => dmString::random(),
  'name'    => dmString::random(),
  'slug'    => dmString::random()
));
$page->Node->insertAsFirstChildOf(dmDb::table('DmPage')->getTree()->fetchRoot());

$expected = $helper->get('script_name_resolver')->get('front');
$t->is(£link($page)->getHref(), $expected = $frontScriptName.'/'.$page->slug, $expected);
$t->is(£link('page:'.$page->id)->getHref(), $expected = $frontScriptName.'/'.$page->slug, $expected);

$t->is(£link('page:'.$page->id.'?var1=val1&var2=val2')->getHref(), $expected = $frontScriptName.'/'.$page->slug.'?var1=val1&var2=val2', $expected);

$t->is(£link('page:'.$page->id.'?var1=val1&var2=val2#anchor')->getHref(), $expected = $frontScriptName.'/'.$page->slug.'?var1=val1&var2=val2#anchor', $expected);

sfConfig::set('sf_debug', true);

$badSource = 'page:9999999999999';
$errorLink = (string)£link($badSource);
$t->is($errorLink, '<a class="link">'.$badSource.' is not a valid link resource</a>', $errorLink);

$page->Node->delete();