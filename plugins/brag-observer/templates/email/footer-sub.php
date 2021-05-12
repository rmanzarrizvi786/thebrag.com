</div>
</td>
        </tr>

        <tr>
          <td valign="top" class="templateFooter" style="background: #ffffff; text-align: center; padding: 10px 0 10px 0;">


            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
              <tbody class="mcnTextBlockOuter">
                <tr>
                  <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" style="max-width:100%;" width="100%" class="mcnTextContentContainer">
                      <tbody>
                        <tr>
                          <td valign="top" class="mcnTextContent" style="padding-top: 0px; padding-bottom: 30px; text-align: center;">
                            <?php if ( isset( $this->observer ) ) :
                              $this->observer->print_social_icons();
                            elseif ( $this instanceof BragObserver ) :
                              $this->print_social_icons();
                            endif; ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>

          </td>
        </tr>

    </tbody>
</table>
</td>
                            </tr>
                            <tr>
                                <td valign="top" class="templateFooter" align="center" style="text-align: center; padding: 5px; background: #333333; font-size: 10px; color: #ffffff;">
                                  <div class="mcnTextContent">
                                    <br>
                                    Copyright &copy; <?php echo date('Y'); ?>
                                    <br>
                                    <div class="vcard">
                                      <span class="org fn">The Brag Media</span>
                                      <div class="adr">
                                        <div class="street-address">Level 2, 9-13 Bibby Street</div>
                                        <span class="locality">Chiswick</span>,
                                        <span class="region">NSW</span>
                                        <span class="postal-code">2046</span>
                                        <div class="country-name">Australia</div>
                                      </div>
                                    </div>
                                  </div>
                                </td>
                            </tr>

                            <tr>
                              <td valign="top" class="templateFooter" style="padding-top:0; padding: 10px 0; color: #dedede; text-align: center; font-size: 10px; background: #333333; ">
                                <div class="mcnTextContent">
                                <?php
                                if ( ! get_user_meta( $user_id, 'oc_token', true ) ) :
                                  $oc_token = md5( $user_id . time() ); // creates md5 code to verify later
                                  update_user_meta( $user_id, 'oc_token', $oc_token );
                                endif;
                                $unserialized_oc_token = [
                                  'id' => $user_id,
                                  'oc_token' => get_user_meta( $user_id, 'oc_token', true ),
                                ]; // makes it into a code to send it to user via email
                                ?>
                                <a target="_blank" href="https://thebrag.com/verify/?a=unsub&oc=<?php echo base64_encode( serialize( $unserialized_oc_token ) ); ?>" style="color: #aaaaaa !important;text-decoration: none;font-size: 10px !important;">Unsubscribe</a>
                                &nbsp;
                                <span style="font-size: 10px !important; color: #666666;">|</span>
                                &nbsp;

                                <a target="_blank" title="Advertise with us" href="https://thebrag.com/media/" target="_blank" style="color: #aaaaaa; text-decoration: none;font-size: 10px !important;">Advertise with us</a>
                              </div>
                              </td>
                            </tr>
                        </table>
                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>
