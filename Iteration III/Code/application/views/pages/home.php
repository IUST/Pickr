<?php
  $is_logged_in = $this->tank_auth->is_logged_in(); 
?>
<div class="container">
	<div id="holder">

    <?php if (!$is_logged_in) { ?>
        <!-- Top Message -->
        <div class="alert alert-error" style="margin: 1%;">
            <!-- <button type="button" class="close" data-dismiss="alert">×</button> -->
            <strong style="margin-right: 1%;">Welcome to Pickr!</strong>
            <span style="margin-right: 0.5%;">Need an account?</span>
            <!-- Button to trigger modal -->
            <a href="#signUp" role="button" class="btn btn-danger" data-toggle="modal"><strong>Sign Up</strong></a>
        </div>
        <!-- END Top Message -->

        <!-- SignUp Form -->
        <div id="signUp" style="width: 500px;" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="signUpLabel" aria-hidden="true">
        
        <?php $attributes = array('id' => 'register-form', 'class' => 'form-horizontal');
          echo form_open('index.php/auth/register', $attributes); ?>

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="signUpLabel">Create Account</h3>
          </div>
          <div class="modal-body">
            <!-- <form class="form-horizontal" id="register-form" action=""> -->
              <div class="control-group">
                <label class="control-label" for="username">Username</label>
                <div class="controls">
                   <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input type="text" id="username" name="username" value="<?php echo set_value('username');?>" maxlength="20" minlength="4" placeholder="Username" spellcheck="false" required />
                   </div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="email">Email</label>
                <div class="controls">
                   <div class="input-prepend">
                    <span class="add-on"><i class="icon-envelope"></i></span>
                    <input type="email" id="email" name="email" value="<?php echo set_value('email');?>" placeholder="Email" spellcheck="false" required />
                   </div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="password">Password</label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input type="password" id="password" name="password" value="<?php echo set_value('password');?>" minlength="6" placeholder="Password" required />
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="confirm_password">Confirm Password</label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input type="password" id="confirm_password" name="confirm_password" value="<?php echo set_value('confirm_password');?>" minlength="6" equalto="#password" placeholder="Confirm Password" required />
                  </div>
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <!-- <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button> -->
            <input type="submit" class="btn btn-large btn-primary" id="register-btn" style="width:100%; font-weight: bold;" value="Sign Up" />
          </div>
          
          <?php echo form_close();?>

        </div>
        <!-- END SignUp Form -->
    <?php } ?>

    <!-- Load Pictures -->
    <div class="gallery">
    <?php
      for($i = 1; $i < 25; $i++) { ?>
        <div class="article">
              <div class="frame">
              <figure class="cap-bot">
                <div class="inner-box" id="pic_<?php echo $i; ?>"<?php if ($is_logged_in) { ?> onMouseOver="ShowActions(this.id)" onMouseOut="HideActions(this.id)" <?php }?>>
                  <?php
                    if ($is_logged_in) { ?>
                    <span class="tool-box">
                        <a href="#pick" role="button" class="btn btn-small" data-toggle="modal"><i class="icon-magnet"></i></a>
                        <a class="btn btn-small" href="#" onClick="Comment(this.parentNode.parentNode.id)"><i class="icon-comment"></i></a>
                        <a class="btn btn-small" href="#" onClick="Like(this.parentNode.parentNode.id)"><i class="icon-thumbs-up"></i></a>
                    </span><?php }?>
                    <a class="pic-link" href="#">
                        <img id="pic_<?php echo $i; ?>" class="lazy" src="<?php echo base_url();?>resources/images/grey.gif" data-original="<?php echo base_url();?>resources/images/main/<?php echo $i; ?>.jpg" alt="pic_<?php echo $i; ?>" onLoad="OnImageLoad(event)" />
                    </a>
                      <figcaption>
                      <span>by unknown photographer</span>
                      <span class="record pull-right">
                          <i class="icon-comment icon-white record-img"></i> <?php echo (2 * $i + 3); ?>
                          <i class="icon-heart icon-white record-img"></i> <?php echo (2 * $i + 1); ?>
                      </span>
                  </figcaption>
                </div>
            </figure>
            </div>
        </div>
    <?php } ?>
    </div>
    <!-- END Load Pictures -->

    <?php
      if ($is_logged_in) { ?>

          <!-- pick Form -->
          <div id="pick" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="pickLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 id="pickLabel">Pick</h3>
            </div>
            <div class="modal-body">
              <a class="pull-left" href="#" style="width: 45%; height: 175px;">
                <img class="thumbnail" style="margin-right: 5px; width: 100%; height: 95%;" src="<?php echo base_url(IMAGES.'220x200.gif'); ?>">
              </a>
              <div class="control-group pull-right" style="width: 50%; margin-bottom: 0;">
                <div class="btn-group">
                  <button style="width: 100%; height: 33px;" id="current-album" class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    <strong style="float: left;">Select Album</strong>
                    <span class="caret" style="float: right;"></span>
                  </button>
                  <ul id="album_list" class="dropdown-menu" style="width: 100%; max-height: 145px; overflow-x: hidden; overflow-y: auto;">
                    <!-- dropdown menu links -->
                    <?php if($albums) {
                      foreach ($albums as $album) { ?>
                        <li><a href="#" onClick="SetCurrentAlbum(this.innerHTML)"><?php echo $album; ?></a></li>
                      <?php }
                    }?>
                    <li class="divider"></li>
                    <li>
                      <form action="http://localhost/pickr/index.php/home/create_album" method="post" id="create-album-form">
                        <input type="text" id="album_name" class="input-small pull-left" style="width: 64%; margin: 0 3% 3% 3%;" placeholder="Album Name" spellcheck="false" />
                        <button type="submit" class="btn pull-right" id="create-album-btn"><strong>Create</strong></button>
                      </form>
                    </li>
                  </ul>
                </div>
                <?php
                $attributes = array('id' => 'pick-form', 'class' => 'form-horizontal');
                echo form_open('index.php/home/add_pic_to_album', $attributes); ?>
                <textarea id="album-description" rows="3" cols="40" style="resize: none; margin-top: 10%; width: 96.5%;" maxlength="50" placeholder="Description" onfocus="ShowDescriptionMessage()" onBlur="HideDescriptionMessage()" spellcheck="false"></textarea>
                <input type="submit" class="btn btn-large btn-primary disabled" data-loading-text="Picking..." id="add-to-album-btn" style="width:100%; margin-top:5%; font-weight: bold;" value="Add Picture to Album" disabled="disabled" />
                <?php form_close(); ?>
              </div>
            </div>
            <div class="modal-footer"> 
            </div>
          </div>
          <!-- END pick Form -->
    <?php } ?>

  </div>
  <button class="btn btn-large btn-block" id="btn-load" type="button"><b>More</b></button>
</div>

<button id="ScrollToTop" class="Button WhiteButton Indicator" type="button" style="display: none;">
  Scroll to Top
</button>