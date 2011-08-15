class KudoMailer < ActionMailer::Base
  default :from => "kudobot@jibjab.net"
  
  def kudo_email(kudo,u)
    @kudo = kudo
    user = User.find(u)
    unless user.blank?
      mail(:to => user.email,
         :subject => "KudoBot has sent you kudos")
    end
  end
  
  def daily_stats(kudos,user)
    @kudos = kudos
     
      mail(:to => user.email,
         :subject => "KudoBot Daily Kudos")
  
  end
end
