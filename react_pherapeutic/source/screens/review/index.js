import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    Image,
    TextInput,
    BackHandler
} from 'react-native';
import constants from '../../utils/constants';
import SubmitButton from '../../components/submitButton';
import styles from './styles';
import { connect } from 'react-redux';
import { Rating, AirbnbRating } from 'react-native-ratings';
import APICaller from '../../utils/APICaller';
import Header from '../../components/Header';
import StarRating from 'react-native-star-rating';
import AwesomeAlert from 'react-native-awesome-alerts';
const Review = (props) => {
    const [reviewText, setreviewText] = useState('');
    const [ratings, setratings] = useState(0);
    const [showAlert, setShowAlert] = useState(false);
    const [alertMessage, setAlert] = useState('Please fill review');

    const { userToken, navigation } = props;

    const { CallReciverName, caller_id } = props.route.params;
    const ratingCompleted = (rating) => {
        console.log("Rating is: " + rating);
        setratings(rating);
    }

    const skip = () => {
        navigation.navigate('Home');
    }
    const reviewHandler = () => {

        let finalObj = { comment: reviewText, rating: ratings.toString(), call_logs_id: caller_id }
        console.log("finalobj", finalObj);
        if (reviewText != '') {
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json',
            };
            console.log("finalobj2", `Bearer ${userToken}`);
            APICaller('clientPostRating', 'POST', finalObj, headers)
                .then(response => {
                    console.log('response logging in => ', response['data']);
                    const { data, message, status, statusCode } = response['data'];
                    if (status === 'success') {
                        //   navigation.navigate('app', { screen: 'RatingList' });
                        navigation.navigate('Home');
                    }

                })


                .catch(error => {
                    console.log('error logging in => ', error);
                    const { data, message, status, statusCode } = error['data'];
                    navigation.navigate('TherapistDetails');
                })
        }
        else {
            setShowAlert(true)
        }
    }
    return (
        <View style={styles.container}>

            <Image
                source={constants.images.background}
                resizeMode={'stretch'}
                style={styles.containerBackground}
            />
            {/* <Header title= {'Rate your experience with Dr.'+CallReciverName} navigation={ navigation} /> */}
            <View style={styles.viewStyle}>
                <Text style={styles.leaveNoteTextHeader}>Rate your experience with Dr. {CallReciverName}</Text>

            </View>
            {/* <AirbnbRating
                showRating
                count={5}
                defaultRating={2}
                size={60}
                reviews={[]}
                selectedColor="rgb(24,173,189)"
                ratingColor='#3498db'
                ratingBackgroundColor="rgb(24,173,189)"
                starContainerStyle="white"
                style={styles.starStyle}
                onFinishRating={ratingCompleted}

            />  */}
            <StarRating
                starSize={60}
                disabled={false}
                maxStars={5}
                rating={parseInt(ratings) || 0}
                starStyle={{ marginTop: 4, paddingTop: 10 }}
                fullStarColor={'"rgb(24,173,189)"'}
                //  emptyStarColor={'white'}
                selectedStar={(rating) => ratingCompleted(rating)}
            />

            <View style={styles.reviewView} >

                <View style={styles.reviewWrap} >

                    <Text style={styles.reviewText1} >Your word helps us to make</Text>
                    <Text style={styles.reviewText2} > pherapeutic better for you</Text>

                    <View style={styles.ReviewField}>
                        {/* <TextInput placeholder={'Write your review'}
                            placeholderTextColor={constants.colors.black}
                            onChangeText={text => setreviewText(text)}
                            style={styles.fieldInput}
                        /> */}
                        <TextInput
                            placeholder={'Write your review'}
                            placeholderTextColor={constants.colors.black}
                            style={[styles.fieldInput]}
                            onChangeText={text => setreviewText(text)}
                            value={reviewText}
                            autoCapitalize={'none'}
                            multiline={true}
                            numberOfLines={10}
                        />
                    </View>

                </View>
            </View>

            <View style={styles.rememberMeView} >
                <View style={{ margin: 5 }}>
                    <SubmitButton
                        title={'SKIP'}
                        size={'Medium'}
                        colors={['rgb(62, 218, 243)', 'rgb(191, 53, 160)']}
                        submitFunction={() => skip()}
                    />

                </View>
                <View style={{ margin: 5 }}>
                    <SubmitButton
                        size={'Medium'}
                        title={'SUBMIT'}
                        colors={['rgb(62, 218, 243)', 'rgb(191, 53, 160)']}
                        submitFunction={() => reviewHandler()}
                    />
                </View>
            </View>
            <AwesomeAlert
                show={showAlert}
                showProgress={false}
                message={alertMessage}
                closeOnTouchOutside={true}
                showConfirmButton={true}
                confirmText="Confirm"
                confirmButtonColor={constants.colors.lightGreen}
                onCancelPressed={() => {
                    setShowAlert(false);
                }}
                onConfirmPressed={() => {
                    setShowAlert(false);
                }}
                onDismiss={() => {
                    setShowAlert(false);
                }}
            />
        </View>


    )
};

const mapStateToProps = (state) => ({
    userToken: state.user.userToken,
});

export default connect(mapStateToProps)(Review);