import React from 'react';
import { connect } from 'react-redux';
import QuestionBot from '../questionBot';
import TherapistStatus from '../therapistStatus';

const Home = (props) => {

  const { userData, navigation } = props;
  if (userData.role == 1)
    return <TherapistStatus navigation={navigation} />
  else
    return <QuestionBot navigation={navigation}  />
}

const mapStateToProps = (state) => ({
  userData: state.user.userData,
  userToken: state.user.userToken
});

export default connect(mapStateToProps)(Home);