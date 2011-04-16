
<h1><?=Language::GetText('subscriptions_header')?></h1>

<?php if ($success): ?>
    <div id="success"><?=$success?></div>
<?php endif; ?>


<?php if ($db->Count($result) > 0): ?>

    <?php while ($row = $db->FetchObj ($result)): ?>

        <?php
        $sub = new Subscription ($row->sub_id);
        $member = new User ($sub->member);
        ?>

        <div class="member">

            <p align="center">
                <a href="<?=HOST?>/members/<?=$member->username?>/" title="<?=$member->username?>"><img src="<?=$member->avatar?>" width="100" height="100" alt="<?=$member->username?>" /></a>
                <a class="large" href="<?=HOST?>/members/<?=$member->username?>/" title="<?=$member->username?>"><?=Functions::CutOff ($member->username,18)?></a>
                <a class="delete confirm" data-node="confirm_subscription" href="<?=HOST?>/myaccount/subscriptions/<?=$member->user_id?>/" title="<?=Language::GetText('unsubscribe')?>"><span><?=Language::GetText('unsubscribe')?></span></a>
            </p>

        </div>

    <?php endwhile; ?>

    <br clear="all" />
    <?=$pagination->Paginate()?>

<?php else: ?>

    <div class="block">
        <strong><?=Language::GetText('no_subscriptions')?></strong>
    </div>

<?php endif; ?>