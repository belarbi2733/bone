/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* 
 * File:   main.cpp
 * Author: Amine
 *
 * Created on July 6, 2017, 12:18 AM
 */

//------------------------------------------------------------------------------------------------------------------------------------------------
//Includes
//------------------------------------------------------------------------------------------------------------------------------------------------
#include "opencv2/core/core.hpp"
#include "opencv2/highgui/highgui.hpp"
#include "opencv2/imgproc/imgproc.hpp"
#include <opencv2/opencv.hpp>
#include <iostream>
#include <stdio.h>
#include <stdlib.h>
#include <fstream> //file creation
#include <string>

using namespace cv;
using namespace std;

//------------------------------------------------------------------------------------------------------------------------------------------------
//Constantes
//------------------------------------------------------------------------------------------------------------------------------------------------
 string FOLDER_NAME = "Result_0_0_0";
//------------------------------------------------------------------------------------------------------------------------------------------------
//Declarations
//------------------------------------------------------------------------------------------------------------------------------------------------

Mat src, src_gray, src_clahe, img_open, nlmeans_rgb, nlmeans_output, in_threshold, src_rgb;
int rows, cols;
//canny
//int lowThresh = 80;
//int highThresh = 143;

//polynome deg
int deg = 3;
//CLAHE
int n = 3;

// NLmeans
int onDenoising = 1;
int h = 9;
int templateWindowSize = 3;
int searchWindowSize = 13;
int max_thresh = 10;

//Meanshift
cv::Mat img_meanshift;
int spatialRad = 4;
int colorRad = 24;
int minSizeMeanshift = 50; //gpu
Mat ms_out;
string winName = "meanshift";

//flood Fill
int loC = 7;
int hoC = 10;

//kmeans
int k = 10;
Mat out_rangeKmeans;

//Open
int open_pos = 5;
int morpho = 2;

//canny
int lowThresh = 60;
int highThresh = 100;
int canny_kernel = 2;

//Ellipse fit
int hr = 0;
int lr = 0;
int morpho_range_type = 2;
int cm_pos = 1;
int blur_k = 2;

int canny_morph = 0;
RNG rng(12345);
int contour_size = 10;
int ellipse_low_size = 40; //36
int ellipse_high_size = 100; //100

int thresh_end = 120;
int thresh_begin = 36;

//Threshold
int thresh_pos = 77;
int thresh_upp = 195;

//floofill(meanshift))
Mat img_floodfill;
//FloodFill
Mat image0, image, gray, mask, mask_hist;
int ffillMode = 1;
int loDiff = 20, upDiff = 20;
int connectivity = 4;
int isColor = true;
bool useMask = false;
int newMaskVal = 255;

//FidnContours
int mode = 0; /* Contour retrieval modes */
int method = 4; /* Contour approximation methods */
int off = 0;


//Coefficient du Polynome
double a0, a1, a2, a3, a4, a5, a6, a7, a8, a9;

Mat curveNew;
int beginCurve, endCurve;
//Profile



//------------------------------------------------------------------------------------------------------------------------------------------------
//Functions header
//------------------------------------------------------------------------------------------------------------------------------------------------

void Denoising(int, void*);
void Clahe_function(int, void*);
void Kmeans_function(int, void*);
void Opening(int, void*);
void Ellipse_fit(int, void*);
static void meanShiftSegmentation(int, void*);
void FloofFill(Mat);
void polynomialFitting(vector<RotatedRect> Data);
void floodFillPostprocess(Mat& img, const Scalar& loDiff, const Scalar& upDiff);
void grayProfile();
int poly(int yc);
//file stream
void file(vector <int> gg);
//void grid(Mat profile, vector <pointz> profile_vec);
//fonction order

bool order(RotatedRect po1, RotatedRect po2) {
    return po1.center.y < po2.center.y;
}

struct point {
    float x;
    float y;
    bool active;
};

//profile

struct pointz {
    float value;
    int x;
    int y;
};
vector <pointz> vertebra_c;

void grid(Mat profile, vector <pointz> profile_vec);

//Fonction flood fill
cv::Mat bwImage;
vector<vector<Point> > contoursf;

void floodFillPostprocess(Mat& img, const Scalar& loDiff, const Scalar& upDiff) {
    CV_Assert(!img.empty());
    Mat img_flood;
    img.copyTo(img_flood);
    RNG rng = theRNG();
    int surface = 0;
    Mat mask(img_flood.rows + 2, img_flood.cols + 2, CV_8UC1, Scalar::all(0));

    for (int y = 0; y < img_flood.rows; y++) {
        for (int x = 0; x < img_flood.cols; x++) {
            if (mask.at<uchar>(y + 1, x + 1) == 0) {
                // int gray= rng(256);
                Scalar newVal(rng(256), rng(256), rng(256));
                surface = floodFill(img_flood, mask, Point(x, y), newVal, 0, loDiff, upDiff, 8);
                cout << "Pixels where painted : " << surface << endl;
            }
        }
    }

   // imshow("FloodFill", img_flood);

}

void Clahe_function(int, void*) {

    Ptr<CLAHE> clahe = createCLAHE();
    if (n > 0) {
        clahe->setClipLimit(n);
        clahe->apply(src_gray, src_clahe);
        cout << "CLAHE Done ! " << endl;
    } else src_gray.copyTo(src_clahe);
  //  imshow("CLAHE", src_clahe);

    //    Kmeans_function(k,0);
    Denoising(n, 0);
}

/** @function thresh_callback */
void Denoising(int, void*) {
    if (onDenoising = 1) {
        fastNlMeansDenoising(src_clahe, nlmeans_output, h + 1, templateWindowSize, searchWindowSize);
        cout << "Denoising Done ! " << endl;
    } else src_clahe.copyTo(nlmeans_output);

    //    Mat elementx = getStructuringElement(MORPH_ELLIPSE, Size(3, 3), Point(1, 1));
    //    morphologyEx(nlmeans_output, nlmeans_output, MORPH_OPEN, elementx);
   // imshow("Denoising", nlmeans_output);
    //   // blur(nlmeans_output, nlmeans_output, Size(3, 3));
    //Opening(0,0);
    Opening(0, 0);
}

void Opening(int, void*) {
    Mat img_bl;
    vector<vector<Point> > contours;
    vector<Vec4i> hierarchy;

    int n = open_pos;

    Mat element = getStructuringElement(MORPH_ELLIPSE, Size(n * 2 + 1, n * 2 + 1), Point(n, n));

    // blur(src_clahe, img_bl, Size(3, 3));
    morphologyEx(nlmeans_output, img_open, morpho, element);
    cout << "Morohology Done ! " << endl;

    // imwrite("opening.jpg", img_open);
   // imshow("Opening", img_open);
    meanShiftSegmentation(0, 0);
    //Kmeans_function(0, 0);
}

static void meanShiftSegmentation(int, void*) {
    cvtColor(img_open, src_rgb, CV_GRAY2RGB);
    pyrMeanShiftFiltering(src_rgb, ms_out, spatialRad, colorRad, 1);
    cout << "pyrMeanShiftFiltering Done ! " << endl;
    Mat ms_colormap;
    applyColorMap(ms_out,  ms_colormap, COLORMAP_HSV);
    imwrite(FOLDER_NAME + "/" + "meanshift.jpg", ms_colormap);
 
    Ellipse_fit(0, 0);
}

void Kmeans_function(int, void*) {
    Mat img_kmeans_src;
    //cvtColor(img_open, src_rgb, CV_GRAY2RGB);
    img_open.copyTo(img_kmeans_src);

    Mat samples(img_kmeans_src.rows * img_kmeans_src.cols, 3, CV_32F);
    for (int y = 0; y < img_kmeans_src.rows; y++)
        for (int x = 0; x < img_kmeans_src.cols; x++)
            for (int z = 0; z < 3; z++)
                samples.at<float>(y + x * img_kmeans_src.rows, z) = img_kmeans_src.at<Vec3b > (y, x)[z];

    Mat labels;
    int attempts = 10;
    Mat centers;


    kmeans(samples, k, labels, TermCriteria(CV_TERMCRIT_ITER | CV_TERMCRIT_EPS, 0.0001, 10000), attempts, KMEANS_PP_CENTERS, centers);
    kmeans(samples, k, labels, TermCriteria(CV_TERMCRIT_ITER | CV_TERMCRIT_EPS, 0.1, 1000), 1, KMEANS_USE_INITIAL_LABELS, centers);
    // kmeans(samples_nn, k, labels, TermCriteria(CV_TERMCRIT_ITER | CV_TERMCRIT_EPS, 0.1, 1000), 4, KMEANS_USE_INITIAL_LABELS, centers);

    Mat img_kmeans(img_kmeans_src.size(), img_kmeans_src.type());

    for (int y = 0; y < img_kmeans_src.rows; y++)
        for (int x = 0; x < img_kmeans_src.cols; x++) {

            int cluster_idx = labels.at<int>(y + x * img_kmeans_src.rows);
            img_kmeans.at<Vec3b > (y, x)[0] = centers.at<float>(cluster_idx, 0);
            img_kmeans.at<Vec3b > (y, x)[1] = centers.at<float>(cluster_idx, 1);
            img_kmeans.at<Vec3b > (y, x)[2] = centers.at<float>(cluster_idx, 2);
        }
    // img_kmeans.copyTo(in_threshold);
    // floodFillPostprocess( img_kmeans, Scalar::all(2) );

    img_kmeans.copyTo(out_rangeKmeans);
   // imshow("Kmeans", img_kmeans);
    //    
    //     inRange(img_kmeans, lowThresh, highThresh, out_rangeKmeans);
    //     imshow("InRage Kmeans",out_rangeKmeans);
    // Ellipse_fit(0, 0);
}


// Function : Threshold + Contours + ellipse

Mat img_ellipse, img_segmentation;


// Function : Threshold + Contours + ellipse

void Ellipse_fit(int, void*) {

    vector<vector<Point> > contours;
    vector<vector<vector<Point> > >contours_table;
    vector<Vec4i> hierarchy;
    vector<RotatedRect> pointCourbe;

    Mat out_threshold, out_range;
    Mat out_blurminEllipse_table, bgr_img;

    cvtColor(ms_out, bgr_img, COLOR_RGB2GRAY);
    Mat out_TEST_range, out_TEST_canny;
    inRange(bgr_img, thresh_begin, 255, out_TEST_range);
    int kc = 2 * canny_kernel + 1;
    Canny(bgr_img, out_TEST_canny, lowThresh, highThresh, kc);

    // Dilate helps to remove potential holes between edge segments
    dilate(out_TEST_canny, out_TEST_canny, Mat(), Point(-1, -1));

    //imshow("Threshold", out_TEST_range);
    //imshow("Canny", out_TEST_canny);
    //Threshold inrange
    Mat morpho_range_out;
    for (int th = 0; th < (thresh_end - thresh_begin); th++) {
        //inRange(bgr_img, thresh_begin + th, 255, out_range);


        out_range = bgr_img >= (th + thresh_begin);

        findContours(out_range, contours, mode, method, Point(off, off));
        contours_table.push_back(contours);
        // cout << " Size of contours : " << contours.size() << endl;

        contours.empty();
    }

    // cout << " Size of contours_table : " << contours_table.size() << endl;

    // Find the rotated rectangles and ellipses for each contour


    vector<vector<RotatedRect> > minEllipse_table(contours_table.size());

    int cs = contour_size;
    for (int j = 0; j < contours_table.size(); j++) {
        vector<RotatedRect> minEllipse(contours_table[j].size());

        for (int i = 0; i < contours_table[j].size(); i++) {
            if (contours_table[j][i].size() > cs)
                minEllipse[i] = fitEllipse(Mat(contours_table[j][i]));
        }
        minEllipse_table.push_back(minEllipse);
        //  cout << " Size of minEllipse : " << minEllipse.size() << endl;
        minEllipse.clear();
    }



    //cout << " Size of minEllipse table : " << minEllipse_table.size() << endl;

    /// Draw contours + rotated rects + ellipses

    int el = ellipse_low_size;
    int eh = ellipse_high_size;

    img_ellipse = Mat::zeros(src_gray.size(), CV_8UC3);
    for (int j = 0; j < minEllipse_table.size(); j++) {

        for (int i = 0; i < minEllipse_table[j].size(); i++) {

            //  if (minEllipse_table[j][i].size.height > el && minEllipse_table[j][i].size.width > el && minEllipse_table[j][i].size.height < eh && minEllipse_table[j][i].size.width < eh && minEllipse_table[j][i].size.height < 1.60 * minEllipse_table[j][i].size.width && minEllipse_table[j][i].center.y < rows-lr && minEllipse_table[j][i].center.y > hr ) {
            if (minEllipse_table[j][i].size.height > el && minEllipse_table[j][i].size.width > el && minEllipse_table[j][i].size.height < eh && minEllipse_table[j][i].size.width < eh && minEllipse_table[j][i].center.y < rows - lr && minEllipse_table[j][i].center.y > hr) {

                pointCourbe.push_back(minEllipse_table[j][i]);
                Scalar color = Scalar(rng.uniform(0, 255), rng.uniform(0, 255), rng.uniform(0, 255)); //BGR

                //        if (minEllipse_table[j][i].center.x > 0 && minEllipse_table[j][i].center.y > 0 && minEllipse_table[j][i].center.x < cols && minEllipse_table[j][i].center.y < rows)
                // contour
                ellipse(img_ellipse, minEllipse_table[j][i], color, 1, CV_AA);

            }
        }
    }

    line(img_ellipse, Point(0, hr), Point(cols, hr), Scalar(170, 170, 170));
    line(img_ellipse, Point(0, rows - lr), Point(cols, rows - lr), Scalar(170, 170, 170));
   // imshow("Ellipse", img_ellipse);
    Mat in_img_rgb;
    cvtColor(src_clahe, in_img_rgb, COLOR_GRAY2RGB);
   
    addWeighted(in_img_rgb, 0.60, img_ellipse, 0.99, 0.0, img_ellipse);
   
    imwrite(FOLDER_NAME + "/" + "ellipse.jpg", img_ellipse);

    Mat curvPoint = Mat::zeros(src_gray.size(), CV_8UC3);
    sort(pointCourbe.begin(), pointCourbe.end(), order);

    // Spine Curve

    polynomialFitting(pointCourbe);
    grayProfile();
    cout << " Polynomial Fitting Done ! " << endl;

}

/*

// inRange(bgr_img, lowThresh, highThresh, out_range);
cout << "InRange Done ! " << endl;
//adaptiveThreshold(out_blur, out_threshold, 255, 1, THRESH_BINARY, kc, lowThresh);
// threshold(out_blur, out_threshold, thresh_pos, thresh_upp, THRESH_BINARY);
Mat element_matrix = getStructuringElement(MORPH_ELLIPSE, Size(cm_pos * 2 + 1, cm_pos * 2 + 1), Point(cm_pos, cm_pos));
morphologyEx(out_range, morpho_range_out, morpho_range_type, element_matrix);
cout << "Inrange Morpho Done ! " << endl;
imshow("Threshold", morpho_range_out);

/// Find contours
//   findContours(morpho_range_out, contours, hierarchy, mode, method, Point(off, off));
cout << "Find contours Done ! " << endl;
// Draw contours
Mat drawing = Mat::zeros(src_gray.size(), CV_8UC3);

for (int i = 0; i < contours.size(); i++) {
    Scalar color = Scalar(rng.uniform(0, 255), rng.uniform(0, 255), rng.uniform(0, 255));
    drawContours(drawing, contours, i, color, 2, 8, hierarchy, 0, Point());
}

/// Show in a window
imshow("Contours", drawing);

// Find the rotated rectangles and ellipses for each contour

vector<RotatedRect> minRect(contours.size());
vector<RotatedRect> minEllipse(contours.size());
vector<RotatedRect> pointCourbe;

int cs = contour_size;

for (int i = 0; i < contours.size(); i++) {

    minRect[i] = minAreaRect(Mat(contours[i]));

    if (contours[i].size() > cs && cs > 3) {
        minEllipse[i] = fitEllipse(Mat(contours[i]));
    }
}

/// Draw contours + rotated rects + ellipses


int el = ellipse_low_size;
int eh = ellipse_high_size;


img_ellipse = Mat::zeros(src_gray.size(), CV_8UC3);
img_segmentation = Mat::zeros(src_gray.size(), CV_8UC3);

Mat weighted, src_RGB;


cvtColor(src_gray, src_RGB, COLOR_GRAY2RGB);
for (int i = 0; i < contours.size(); i++) {
    Scalar color = Scalar(rng.uniform(0, 255), rng.uniform(0, 255), rng.uniform(0, 255)); //BGR

    if (minEllipse[i].size.height > el && minEllipse[i].size.width > el && minEllipse[i].size.height < eh && minEllipse[i].size.width < eh) {

        if (minEllipse[i].center.x > 0 && minEllipse[i].center.y > 0 && minEllipse[i].center.x < cols && minEllipse[i].center.y < rows)
            pointCourbe.push_back(minEllipse[i]);

        // contour
        drawContours(img_segmentation, contours, i, color, CV_FILLED, 4, vector<Vec4i > (), 0, Point());
        circle(img_segmentation, minEllipse[i].center, 2, Scalar(0, 0, 255), 2, 8); //BGR

        // rotated rectangle
        Point2f rect_points[4];
        minRect[i].points(rect_points);
        for (int j = 0; j < 4; j++)
            line(img_ellipse, rect_points[j], rect_points[(j + 1) % 4], color, 1, 8);
    }
}

Mat curvPoint = Mat::zeros(src_gray.size(), CV_8UC3);
sort(pointCourbe.begin(), pointCourbe.end(), order);
char c[2];

for (int i = 0; i < pointCourbe.size(); i++) {
    Scalar color = Scalar(rng.uniform(0, 255), rng.uniform(0, 255), rng.uniform(0, 255)); //BGR
    circle(curvPoint, pointCourbe[i].center, 2, CV_RGB(0, 255, 0), 2, 8);
    sprintf(c, "%i", i);
    putText(curvPoint, c, Point(pointCourbe[i].center.x + 5, pointCourbe[i].center.y), FONT_HERSHEY_PLAIN, 1, CV_RGB(0, 255, 0), 1, 4);
    // ellipse(img_ellipse, pointCourbe[i], color, 1, CV_AA);
}

imshow("CurvePoint", curvPoint);

// Spine Curve

polynomialFitting(pointCourbe);
cout << " Polynomial Fitting Done ! " << endl;
imshow("Ellipse", img_ellipse);

addWeighted(img_segmentation, 0.50, src_RGB, 0.8, 0.0, weighted);
imshow("Weignted", weighted);
 }

 * */

void polynomialFitting(vector<RotatedRect> Data) {




    int nbData = 0;


    //  int* tableau = new int[uneVariableNonConstante]


    Mat curve, curve_ext;
    cvtColor(src_gray, curve, COLOR_GRAY2RGB);
    curve.copyTo(curve_ext);


    vector<double> RMS;
    RMS.push_back(0);
    bool OK = true;
    while (OK) {
        OK = false;
        int nbData = Data.size();
        cout << "Data size : " << nbData << endl;

        if (nbData > 3) {
            double* x = new double[nbData];
            double* y = new double[nbData];


            for (int i = 0; i < nbData; i++) {
                y[i] = Data[i].center.x;
                x[i] = Data[i].center.y;
            }
            double M, N, P, Q, R, S, T, U, V, W, C11, C12, C13, C14, C15, C16, C17, C18; //coefficients
            double Y1, Y2, Y3, Y4, Y5, Y6, Y7, Y8, Y9, Y10;

            M = N = P = Q = R = S = T = U = V = W = C11 = C12 = C13 = C14 = C15 = C16 = C17 = C18 = Y1 = Y2 = Y3 = Y4 = Y5 = Y6 = Y7 = Y8 = Y9 = Y10 = 0;
            for (int i = 0; i < nbData; ++i) {
                double x_carre = x[i] * x[i];
                double x_cube = x_carre * x[i];
                double x_quatre = x_cube * x[i];
                double x_cinq = x_quatre * x[i];
                double x_six = x_cinq * x[i];
                double x_sept = x_six * x[i];
                double x_huit = x_sept * x[i];
                double x_neuf = x_huit * x[i];
                double x_dix = x_neuf * x[i];
                double x_11 = x_dix * x[i];
                double x_12 = x_11 * x[i];
                double x_13 = x_12 * x[i];
                double x_14 = x_13 * x[i];
                double x_15 = x_14 * x[i];
                double x_16 = x_15 * x[i];
                double x_17 = x_16 * x[i];
                double x_18 = x_17 * x[i];
                M += x[i];
                N += x_carre;
                P += x_cube;
                Q += x_quatre;
                R += x_cinq;
                S += x_six;
                T += x_sept;
                U += x_huit;
                V += x_neuf;
                W += x_dix;
                C11 += x_11;
                C12 += x_12;
                C13 += x_13;
                C14 += x_14;
                C15 += x_15;
                C16 += x_16;
                C17 += x_17;
                C18 += x_18;
                Y1 += y[i];
                Y2 += x[i] * y[i];
                Y3 += x_carre * y[i];
                Y4 += x_cube * y[i];
                Y5 += x_quatre * y[i];
                Y6 += x_cinq * y[i];
                Y7 += x_six * y[i];
                Y8 += x_sept * y[i];
                Y9 += x_huit * y[i];
                Y10 += x_neuf * y[i];
            }
            // create a 3x3 double-precision identity matrix
            switch (deg) {
                    // 5th : nbData,M,N,P,Q,R,  M,N,P,Q,R,S,  N,P,Q,R,S,T,  P,Q,R,S,T,U,  Q,R,S,T,U,V,  R,S,T,U,V,W); // 4th : nbData,M,N,P,Q,  M,N,P,Q,R,  N,P,Q,R,S,  P,Q,R,S,T,  Q,R,S,T,U);//3rd : nbData, M, N, P, M, N, P, Q, N, P, Q, R, P, Q, R, S);   //2nd : nbData,M,N,M,N,P,N,P,Q); 

                case 3:
                {
                    Mat X = (Mat_<double>(deg + 1, deg + 1) << nbData, M, N, P, M, N, P, Q, N, P, Q, R, P, Q, R, S);
                    Mat Y = (Mat_<double>(deg + 1, 1) << Y1, Y2, Y3, Y4);
                    Mat A = X.inv(DECOMP_LU) * Y;
                    //Coefficients du polynome
                    a0 = A.at<double>(0, 0);
                    a1 = A.at<double>(1, 0);
                    a2 = A.at<double>(2, 0);
                    a3 = A.at<double>(3, 0);
                    a4 = 0.0; //A.at<double>(4, 0);
                    a5 = 0.0; //A.at<double>(5, 0);
                    a6 = 0.0;
                    a7 = 0.0;
                    a8 = 0.0;
                    a9 = 0.0;
                }
                    break;
                case 4:
                {
                    Mat X = (Mat_<double>(deg + 1, deg + 1) << nbData, M, N, P, Q, M, N, P, Q, R, N, P, Q, R, S, P, Q, R, S, T, Q, R, S, T, U);
                    Mat Y = (Mat_<double>(deg + 1, 1) << Y1, Y2, Y3, Y4, Y5);
                    Mat A = X.inv(DECOMP_LU) * Y;
                    //Coefficients du polynome
                    a0 = A.at<double>(0, 0);
                    a1 = A.at<double>(1, 0);
                    a2 = A.at<double>(2, 0);
                    a3 = A.at<double>(3, 0);
                    a4 = A.at<double>(4, 0);
                    a5 = 0.0; //A.at<double>(5, 0);
                    a6 = 0.0;
                    a7 = 0.0;
                    a8 = 0.0;
                    a9 = 0.0;
                }
                    break;
                case 5:
                {
                    Mat X = (Mat_<double>(deg + 1, deg + 1) << nbData, M, N, P, Q, R, M, N, P, Q, R, S, N, P, Q, R, S, T, P, Q, R, S, T, U, Q, R, S, T, U, V, R, S, T, U, V, W);
                    Mat Y = (Mat_<double>(deg + 1, 1) << Y1, Y2, Y3, Y4, Y5, Y6);
                    Mat A = X.inv(DECOMP_LU) * Y;
                    //Coefficients du polynome
                    a0 = A.at<double>(0, 0);
                    a1 = A.at<double>(1, 0);
                    a2 = A.at<double>(2, 0);
                    a3 = A.at<double>(3, 0);
                    a4 = A.at<double>(4, 0);
                    a5 = A.at<double>(5, 0);
                    a6 = 0.0;
                    a7 = 0.0;
                    a8 = 0.0;
                    a9 = 0.0;
                }
                    break;
                default:
                    cout << "Polynom degree not specified !" << endl;


            }





            // int dimA = A.dims;
            // Mat Xinv = X.inv(DECOMP_LU);

            //cout << "X = "<< endl << " "  << X << endl << endl; 
            // cout<<"determinant(X)="<<determinant(X)<<"\n";
            // cout << "Y = "<< endl << " "  << Y << endl << endl; 
            // cout << "A = "<< endl << " "  << A << endl << endl;
            //  cout << "Xinv = "<< endl << " "  << Xinv << endl << endl;



            //Calculate the Root Means Squares RMS


            double rms = 0;
            for (int i = 0; i < nbData; i++) {
                rms += pow((Data[i].center.x - (a0 + a1 * Data[i].center.y + a2 * pow(Data[i].center.y, 2) + a3 * pow(Data[i].center.y, 3) + a4 * pow(Data[i].center.y, 4) + a5 * pow(Data[i].center.y, 5))), 2);
            }
            rms = pow(rms / nbData, 0.5);
            RMS.push_back(rms);
            cout << "Root Means Squares : " << rms << endl;
            cout << "Root Means Squares difference : " << abs(RMS.back() - RMS[RMS.size() - 2]) << endl;
            // double termin= end(RMS);

            if (abs(RMS.back() - RMS[RMS.size() - 2]) > 1) {

                OK = true;

                for (int i = 0; i < Data.size(); i++) {

                    double dd = 0;
                    dd = Data[i].center.x - (a0 + a1 * Data[i].center.y + a2 * pow(Data[i].center.y, 2) + a3 * pow(Data[i].center.y, 3) + a4 * pow(Data[i].center.y, 4) + a5 * pow(Data[i].center.y, 5));
                    if (abs(dd) > 1.50 * RMS.back()) {
                        Data.erase(Data.begin() + i);
                        i--;
                    }
                }
            }
        } else (cout << " Data number < 3 !! ");
    }




    //Localisation des IVD
    //    for (int i = 0; i < nbData-1; i++)
    //             circle(curve, Point((y[i]+y[i+1])/2,(x[i]+x[i+1])/2), 2, CV_RGB(0,0, 255), -1,8,1);
    //  
    cout << " Curve Done !" << endl;
    // Mat showImageCurve;

    //addWeighted(curve, 0.90, img_ellipse, 0.7, 0.0, showImageCurve);

    //Dessin polynome
    sort(Data.begin(), Data.end(), order);
    double d = 0;
    beginCurve = MAX(Data[0].center.y - 10, hr);

    endCurve = Data[Data.size() ].center.y + 20;
    cout << "beginCurve " << beginCurve << endl;
    cout << "endCurve " << endCurve << endl;


    for (int c = beginCurve; c < endCurve && c < rows; c++) {

        d = a0 + (a1 * c) + (a2 * c * c) + (a3 * c * c * c) + (a4 * c * c * c * c) + (a5 * c * c * c * c * c);
        circle(curve, cvPoint(d, c), 2, CV_RGB(255, 0, 0), -1, CV_AA, 0);
        circle(curve_ext, cvPoint(d, c), 15, CV_RGB(255, 255, 51), -1, CV_AA, 0);
    }

    for (int i = 0; i < Data.size(); i++) {
        circle(curve, Point(Data[i].center.x, Data[i].center.y), 2, CV_RGB(0, 255, 0), 2, CV_AA, 0);
        // circle(src_RGB,minEllipse[i].center,2,Scalar(0,0,255),2,8);//BGR
        //  ellipse(curve, Data[i], CV_RGB(0, 0, 255), 1, CV_AA);
    }
    Mat weighted;

    addWeighted(curve_ext, 0.3, curve, 0.99, 0.0, weighted);
    
    imwrite(FOLDER_NAME +"/" + "out_img.jpg", weighted);
   // imshow("Curve", curve);
   // imshow("weighted", weighted);
    //      for (int i = 0; i<Data.size(); i++) {
    //        pointz elem;
    //        elem.x = Data[i].center.x;
    //        elem.y = Data[i].center.y;
    //
    //     elem.value =  ((src_gray.at<uchar>(elem.y,  elem.x - 1) + src_gray.at<uchar>(elem.y,  elem.x) + src_gray.at<uchar>(elem.y,  elem.x + 1))/ 3);
    //        vertebra_c.push_back(elem);
    //
    //
    //    }


    curve.release();
    curve_ext.release();
    Data.clear();
}
void file(vector <pointz> gg){
    ofstream fichier("signal.txt", ios::out | ios::trunc); // ouverture en écriture avec effacement du fichier ouvert

    if (fichier) // si l'ouverture a réussi
    {
        fichier << "X\tY" << endl;
        for (int i = 0; i < gg.size(); i++) {
            fichier << gg[i].y<<"\t" << gg[i].x << endl;
        }

        fichier.close(); // on referme le fichier
    } else // sinon
        cerr << "Erreur à l'ouverture !" << endl;
}

void grayProfile() {

    Mat k_profile(300, 3 * (rows - hr - lr), CV_8UC3, CV_RGB(255, 255, 255));
    Mat g_profile(300, 3 * (rows - hr - lr), CV_8UC3, CV_RGB(255, 255, 255));

    int xc = 0;
    // vector <int> g;

    // vector <pointz> k_profile_vec;
    vector <pointz> g_profile_vec;
    vector <pointz> k_profile_vec;
    Mat g_img, k_img;
    cvtColor(ms_out, k_img, COLOR_RGB2GRAY);
    src_clahe.copyTo(g_img);
    // GaussianBlur(g_img, g_img, Size(3, 3), 1, 1);
    // imshow("Profile image",g_img);

    for (int yc = beginCurve; yc < endCurve && yc < rows; yc++) {
        pointz elem;
        xc = poly(yc);

        elem.x = xc;
        elem.y = yc;

        // elem.value = (g_img.at<uchar>(yc, xc - 6) + g_img.at<uchar>(yc, xc - 5) + g_img.at<uchar>(yc, xc - 4) + g_img.at<uchar>(yc, xc - 3) + g_img.at<uchar>(yc, xc - 2) + g_img.at<uchar>(yc, xc - 1) + g_img.at<uchar>(yc, xc) + g_img.at<uchar>(yc, xc + 1) + g_img.at<uchar>(yc, xc + 2) + g_img.at<uchar>(yc, xc + 3) + g_img.at<uchar>(yc, xc + 4) + g_img.at<uchar>(yc, xc + 5) + g_img.at<uchar>(yc, xc + 6)) / 13;
        elem.value = (g_img.at<uchar>(yc - 1, xc - 1) + g_img.at<uchar>(yc - 1, xc) + g_img.at<uchar>(yc - 1, xc + 1) + g_img.at<uchar>(yc, xc - 1) + g_img.at<uchar>(yc, xc) + g_img.at<uchar>(yc, xc + 1) + g_img.at<uchar>(yc + 1, xc - 1) + g_img.at<uchar>(yc + 1, xc) + g_img.at<uchar>(yc + 1, xc + 1)) / 9;
        // elem.value = (g_img.at<uchar>(yc, xc - 1) + g_img.at<uchar>(yc, xc) + g_img.at<uchar>(yc, xc + 1) ) / 3;

        //   elem.value = g_img.at<uchar>(yc, xc);
        // cout<< "Gray : "  <<elem.value<<endl;        
        g_profile_vec.push_back(elem);


        //elem.value = (k_img.at<uchar>(yc, xc - 2) + k_img.at<uchar>(yc, xc - 1) + k_img.at<uchar>(yc, xc) + k_img.at<uchar>(yc, xc + 1) + k_img.at<uchar>(yc, xc + 2)) / 5;
        elem.value = (k_img.at<uchar>(yc, xc - 6) + k_img.at<uchar>(yc, xc - 5) + k_img.at<uchar>(yc, xc - 4) + k_img.at<uchar>(yc, xc - 3) + k_img.at<uchar>(yc, xc - 2) + k_img.at<uchar>(yc, xc - 1) + k_img.at<uchar>(yc, xc) + k_img.at<uchar>(yc, xc + 1) + k_img.at<uchar>(yc, xc + 2) + k_img.at<uchar>(yc, xc + 3) + k_img.at<uchar>(yc, xc + 4) + k_img.at<uchar>(yc, xc + 5) + k_img.at<uchar>(yc, xc + 6)) / 13;
        k_profile_vec.push_back(elem);

        //  g.push_back(k_profile.at<uchar>(yc, xc));
        file(g_profile_vec);


    }


    grid(k_profile, k_profile_vec);
    grid(g_profile, g_profile_vec);
    
    imwrite(FOLDER_NAME + "/" + "g_profile.jpg", g_profile);
    //   imshow("Mean-shift Profile", k_profile);
    //   imshow("Gray Profile", g_profile);

    k_profile.release();
    g_profile.release();


}



int poly(int c) {
    return a0 + (a1 * c) + (a2 * c * c) + (a3 * c * c * c) + (a4 * c * c * c * c) + (a5 * c * c * c * c * c);

}



//Grid

void grid(Mat profile, vector <pointz> profile_vec) {
    int l = 50;
    int dist = 50;
    Scalar grid1 = Scalar(235, 235, 235);
    Scalar grid2 = Scalar(170, 170, 170);
    int midist = 25;

    int width = profile.size().width;
    int height = profile.size().height;

    for (int i = height - l; i >= 0; i -= midist)
        cv::line(profile, Point(l, i), Point(width, i), grid1);

    for (int i = l; i <= width - l; i += midist)
        cv::line(profile, Point(i, 0), Point(i, height - l), grid1);

    for (int i = height - l; i >= 0; i -= dist)
        cv::line(profile, Point(l, i), Point(width, i), grid2);

    for (int i = l; i <= width; i += dist)
        cv::line(profile, Point(i, 0), Point(i, height - l), grid2);


    for (int i = 1; i < profile_vec.size(); i++) {

        line(profile,
                Point(3 * (i - 1) + l, height - 1.25 * profile_vec[i - 1].value - l),
                Point(3 * i + l, height - 1.25 * profile_vec[i].value - l),
                CV_RGB(255, 0, 0), 1, CV_AA, 0);

    }

    //      Mat centers_show;
    //         src_gray.copyTo(centers_show);
    //     for (int i = 0; i < vertebra_c.size(); i++) {
    //           circle(profile,
    //                   Point(3 * vertebra_c[i].y   ,   height - 1.25*profile_vec[vertebra_c[i].y].value - l),
    //                   1,
    //                   CV_RGB(0,0,255), 2, CV_AA);
    //    
    //       
    //         circle(centers_show,
    //                   Point(vertebra_c[i].x  , vertebra_c[i].y),
    //                   1,
    //                   CV_RGB(0,0,255), 2, CV_AA);
    //    
    //     }
    //    imshow("Centers",centers_show);

    char cc[2];

    for (int i = height - l; i > 0; i -= dist) {
        sprintf(cc, "%i", height - l - i);
        putText(profile, cc, Point(l / 4, i), 2, 0.5, CV_RGB(0, 0, 0), 1, 4);
    }


}




/*
 
void ivd(){
    
   // file(g);  //exportation du signal






   low point
    for (int i = 1; i < g.size(); i++) {
        if(g[i]<50 )
           
           circle(profile,
                   Point(3 * i + l, height - 2 * g[i] - l),
                   1,
                   CV_RGB(0,0,255), 1, CV_AA);
    
       }
    

   //segment detection
   vector <int> T;
   int j = 0;
 //  float midle;
   T.push_back(0);
   for (int i = 1; i < g.size(); i++) {
        

       if (g[i] != g[i - 1]) {

           T.push_back(i);
          // cout<<"end point here : "<< i<<endl;
//             circle(profile,
//                Point(3 * i+l, height-2*g[i]-l),
//                2,
//                CV_RGB(0,0,0), 2, CV_AA);
           
       }
   }
   
   // detection IVD
    
   vector<int> centers;
   Mat ivd;
   src.copyTo(ivd);
   for(int i=0;i<T.size();i++){
       int midle=(T[i]+T[i+1])/2;
       centers.push_back(midle);
        if(g[midle]<50 ){
     circle(profile,
               Point(3*midle+l, height-2*g[midle]-l),
               2,
               CV_RGB(0,255,0), 2, CV_AA);
         circle(ivd,
               Point(profile_vec[midle].x,profile_vec[midle].y ),
               2,
               CV_RGB(0,0,255), 3, CV_AA);
        
        
        }}
   imshow("IVD",ivd);
    
   
     


    
       
int N=g.size();  
   Mat out(2*height, 3*N, CV_8UC3, CV_RGB(255, 255, 255));
double output[N];   
    
   output[0]=g[0]+110;
 for (int i = 1; i < N; i++) {
        
     output[i]=g[i]+g[i-1];
      line(out,
             Point(3 * (i - 1) + l, 2*height - 2 * output[i - 1] - l),
             Point(3 * i + l, 2*height - 2 * output[i] - l),
             CV_RGB(255, 0, 255), 1, CV_AA, 0);
       
 }

     imshow("Out", out);
    imshow("Gray src",grayImg);
       imshow("IVD Points", ivd);

}

 */

/** @function main */

int main(int argc, char** argv) {

    /**
     * 
     * @param argc : ./vertebra_ent17_noui 
     * @param argv :
     * argv[1] image.jpg
     * argv[2] createTrackbar("Clip", "CLAHE", &n, 10, Clahe_function);
     * 
     * argv[3] createTrackbar("spatialRad", "Meanshift", &spatialRad, 20, meanShiftSegmentation);
     * argv[4] createTrackbar("colorRad", "Meanshift", &colorRad, 60, meanShiftSegmentation); 
     *  
     * argv[5] createTrackbar("Truct_Elem", "Opening", &open_pos, 10, Opening);
     * argv[6] createTrackbar("Type", "Opening", &morpho, 6, Opening);
     * 
     * argv[7] createTrackbar("Hight size", "Ellipse", &ellipse_high_size, 200, Ellipse_fit);
     * argv[8] createTrackbar("Low size", "Ellipse", &ellipse_low_size, 200, Ellipse_fit);
     * 
     * argv[9] createTrackbar("Degree", "Curve", &deg, 5, Ellipse_fit);
     * 
     * @return images 
     */

    /// Load source image and convert it to gray
    
    src_gray = imread(argv[1], IMREAD_GRAYSCALE);
    FILE* fichier = NULL;

    fichier = fopen("/root/sharefolder/file_adv.txt", "r");
    if (fichier ==NULL)
    {
        cout << "error" << endl;
        exit(0);
    }
    fscanf(fichier,"%d %d %d %d %d %d %d %d",&n,&morpho,&open_pos,&spatialRad,&colorRad,&ellipse_high_size,&ellipse_low_size,&deg);
    fclose(fichier);
    /*std::ostringstream sss1,sss2,sss3,sss4,sss5,sss6,sss7,sss8;
    sss1 << n;
    sss2 << morpho;
    sss3 << open_pos;
    sss4 << spatialRad;
    sss5 << colorRad;
    sss6 << ellipse_high_size;
    sss7 << ellipse_low_size;
    sss8 << deg;
    if (argc > 2) {
        
        n = atoi(argv[2]); //argv[2] createTrackbar("Clip", "CLAHE", &n, 10, Clahe_function);
        
        morpho = atoi(argv[3]); // createTrackbar("Type", "Opening", &morpho, 6, Opening);
        open_pos = atoi(argv[4]); // createTrackbar("Truct_Elem", "Opening", &open_pos, 10, Opening);
        
        spatialRad = atoi(argv[5]); //  argv[3] createTrackbar("spatialRad", "Meanshift", &spatialRad, 20, meanShiftSegmentation);
        colorRad = atoi(argv[6]); //createTrackbar("colorRad", "Meanshift", &colorRad, 60, meanShiftSegmentation); 

        ellipse_high_size = atoi(argv[7]); // createTrackbar("Hight size", "Ellipse", &ellipse_high_size, 200, Ellipse_fit);
        ellipse_low_size = atoi(argv[8]); // createTrackbar("Low size", "Ellipse", &ellipse_low_size, 200, Ellipse_fit);

        deg = atoi(argv[9]); // createTrackbar("Degree", "Curve", &deg, 5, Ellipse_fit);
    }*/
    //folderName = folderName + "0_0_0";
    string folderCreateCommand = "mkdir " + FOLDER_NAME;
    system(folderCreateCommand.c_str());
   
    if (src_gray.data) {
    
        cout << "Image open succeeded " << endl;
        rows = src_gray.rows;
        cols = src_gray.cols;
        cout << "Image source size : " << " X--> " << cols << " Y--> " << rows << endl;
        
        // call the 1st function which calls the 2nd and so on...
        Clahe_function(0, 0);
        
    } else cerr << "No image opened !\n";
    
    
    // save images 
   // string path = FOLDER_NAME +"/" + "source.jpg";
    imwrite(FOLDER_NAME +"/" + "in_img.jpg", src_gray);
  
    waitKey(0);
    return (0);
}
