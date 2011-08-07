class ApplicationController < ActionController::Base
  protect_from_forgery
  
  def is_admin
    unless (current_user.admin?)
      redirect_to "/"
    end
  end
end
