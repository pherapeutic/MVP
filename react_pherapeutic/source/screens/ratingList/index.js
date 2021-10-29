import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    FlatList,
    Dimensions,
    Image,
} from 'react-native';
import styles from './styles';
import APICaller from '../../utils/APICaller';
import { connect } from 'react-redux';
import constants from '../../utils/constants';
import { AirbnbRating } from 'react-native-ratings';
import Header from '../../components/Header';
import StarRating from 'react-native-star-rating';
const RatingList = (props) => {
    const [data, setData] = useState([]);
    const [average, setAverage] = useState(0);
    const { userToken, userData, navigation } = props;
    useEffect(() => {
        getList();
    }, []);
    const getList = () => {
        let headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${userToken}`,
            Accept: 'application/json',
        };
        APICaller('getRating', 'GET', null, headers)
            .then((response) => {
                console.log("response is", response['data']);
                setData(response['data']['data'])
                getAverage(response['data']['data']);
            })
            .catch((error) => {
                console.log('error=> ', error);
            });

    }
    const getAverage = (data) => {
        const result = data.filter(obj => (obj == null ? "" : obj.rating));
        var sum = 0;
        for (var i = 0; i < result.length; i++) {
            sum += parseInt(result[i].rating, 10);
        }
        let summ = parseInt(sum)
        let len = parseInt(result.length)

        var avg = summ / len;
        setAverage(avg)
    }

    return (
        <View style={styles.container}>
            <Image
                resizeMode={'stretch'}
                source={constants.images.background}
                style={styles.containerBackground}
            />

            <Header title="Rating & Reviews" navigation={navigation} />
            <View style={styles.content}>
                <View style={styles.cardStyle}>
                    <View style={{ flexDirection: "row", paddingBottom: 10, height: "50%" }}>

                        <View style={{ flex: 0.8, paddingLeft: "5%", marginTop: "5%" }}>
                            <Image
                                style={styles.imageView}
                                source={
                                    userData.image ? { uri: userData.image } : constants.images.defaultUserImage
                                }


                            />
                        </View>

                        <View>
                            <Text style={{ fontSize: 14, color: "black", flexDirection: "row", paddingTop: "18%" }}>{userData.first_name} {userData.last_name}</Text>

                            <View style={{ padding: 10, paddingLeft: 10, width: "90%", flexDirection: "row", marginTop: "8%", marginRight: "5%", borderColor: "rgb(24,173,189)", borderRadius: 4, backgroundColor: "rgb(24,173,189)" }}>
                                <Image
                                    source={constants.images.Profile}
                                    style={{ tintColor: 'white', paddingRight: 10, width: 12, height: 15, alignSelf: "center" }}
                                />
                                <Text style={{ fontSize: 14, color: "white", textAlign: "left", paddingLeft: "5%", alignSelf: "center" }}>{parseInt(average)}</Text>

                                {/* <AirbnbRating
                                    showRating={false}
                                    isDisabled={true}
                                    count={5}
                                    size={12}
                                    defaultRating={average}

                                    selectedColor="white"
                                    ratingColor='#3498db'
                                    ratingBackgroundColor="rgb(24,173,189)"
                                    starContainerStyle="white"

                                /> */}
                                <StarRating
                                    starSize={15}
                                    disabled={true}
                                    maxStars={5}
                                    rating={parseInt(average) || 0}
                                    starStyle={{ color: 'white', marginTop: 4 }}

                                //selectedStar={(rating) => this.onStarRatingPress(rating)}
                                />

                            </View>

                        </View>
                    </View>
                    <FlatList
                        data={data}
                        keyExtractor={(item, index) => {
                            return index.toString();
                        }}
                        renderItem={({ item }) => (
                            item == null ?
                                null
                                : <View style={{ borderBottomColor: "rgb(245,245,245)", borderStyle: 'dashed', borderBottomWidth: 1, alignItems: "flex-start", paddingLeft: 20 }}>

                                    <AirbnbRating
                                        showRating
                                        count={5}
                                        size={15}
                                        defaultRating={item.rating}
                                        isDisabled={true}
                                        reviews={[]}
                                        selectedColor="rgb(24,173,189)"
                                        ratingColor='#3498db'
                                        ratingBackgroundColor="rgb(24,173,189)"
                                        starContainerStyle="white"
                                        style={styles.starStyle}


                                    />

                                    <Text style={{ paddingLeft: 5 }}>{item.comment}</Text></View>
                        )}
                    />
                </View>

            </View>
        </View>
    );
};

const mapStateToProps = (state) => ({

    userToken: state.user.userToken,
    userData: state.user.userData,
});
export default connect(mapStateToProps)(RatingList);
