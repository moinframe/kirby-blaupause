<?php

/**
 * @var \Femundfilou\AssetManager\AssetManager $assetManager
 * @var \Kirby\Cms\App $kirby
 * @var \Kirby\Cms\Site $site
 */

$assetManager->add('js', vite()->asset('frontend/snippets/header.ts'), ['type' => 'module']);
?>
<a href="#page" class="skip-link"><?= t('button.skip-menu') ?></a>

<scroll-header class="grid-reset" id="page-header">
	<div class="span-full flow-row has-items-center has-px-m-l">
		<button type="button" class="button is-hidden:m has-ml-a" aria-expanded="false" aria-controls="mainnav">
			<?= t('button.menu') ?> <?= icon('close', '1.25em') ?>
		</button>
		<?php if ($menu = $site->mainmenu()->toPages()) : ?>
			<nav id="mainnav" aria-label="<?= t('aria.nav.main') ?>">
				<ul class="menu">
					<?php foreach ($menu as $p) : ?>
						<?php $hasChildren = $p->hasListedChildren(); ?>
						<li class="<?php e($hasChildren, 'has-submenu') ?> <?php e($p->isOpen(), 'is-active') ?>"
							data-pid="<?= $p->uid() ?>">
							<a href="<?= $p->url() ?>" data-text="<?= $p->title() ?>">
								<span><?= $p->title() ?></span>
							</a>
							<?php if ($hasChildren) : ?>
								<ul class="submenu" aria-label="<?= $p->title() ?>">
									<?php foreach ($p->children()->listed() as $child) : ?>
										<li <?php e($child->isOpen(), 'class="is-active"') ?>>
											<a href="<?= $child->url() ?>" data-text="<?= $child->title() ?>">
												<span><?= $child->title() ?></span>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>
</scroll-header>