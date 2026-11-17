<?php
class reviewViewPts extends viewPts
{
  public function getCardHtml($card)
  {
    $this->assign('card', $card);
    return parent::getContent('reviewCard');
  }
}
