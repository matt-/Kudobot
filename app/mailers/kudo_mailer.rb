class KudoMailer < ActionMailer::Base
  default :from => "kudobot@jibjab.net"
  
  def kudo_email(kudo,u)
    @kudo = kudo
    user = User.find(u)
    unless user.blank?
      if Rails.env == "development"
        mail(:to => user.email,
          :subject => "KudoBot has sent you kudos")
      else
        mail(:to => current_user.email,
          :subject => "KudoBot has sent you kudos [MEANT FOR " + u.email + "]")
      end
    end
  end
  
  def daily_stats(kudos,user)
    @kudos = kudos
     
      mail(:to => user.email,
         :subject => "KudoBot Daily Kudos")
  
  end
end
